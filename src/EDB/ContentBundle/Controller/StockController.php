<?php

namespace EDB\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;


class StockController extends Controller
{
    private function returnRequest($template, $data)
    {
        $view = View::create($data);
        if (strpbrk(":", $template))
            $view->setTemplate(new TemplateReference($template));
        else
            $view->setTemplate(new TemplateReference('EDBContentBundle', 'Resource', $template));

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    private function returnOk($template, $data)
    {
        $data['success'] = true;

        return $this->returnRequest($template, $data);
    }


    private function returnError($message)
    {
        $data = array();

        $data['success'] = false;
        $data['message'] = $message;

        return $this->returnRequest('error', $data);
    }


    private function identifierAsArticle($identifier)
    {
        if (is_object($identifier))
            return $identifier;

        $o = $this->get('edbcontentbundle.articles')->getById($identifier);

        return $o;
    }


    private function identifierAsStockItem($identifier)
    {
        if (is_object($identifier))
            return $identifier;

        $o = $this->get('edbcontentbundle.stock')->getById($identifier);

        return $o;
    }


    public function doAction(Request $request, $article_id)
    {
        $method = $request->getMethod();

        if ($this->get('security.context')->isGranted('ROLE_USER') === false) {
            return $this->returnError('access denied');
        }

        $o = $this->identifierAsArticle($article_id);
        if (!$o) {
            return $this->returnError('article not found, identifier '.$article_id.' is invalid');
        }

        if ($method == 'GET') {
            return $this->getAction($request, $o);
        } else if ($method == 'PUT') {
            return $this->putAction($request, $o);
        } else if ($method == 'DELETE') {
            return $this->deleteAction($request, $o);
        }

        return $this->returnError('invalid method');
    }


    private function getAction(Request $request, $article)
    {
        $article_id = $article->getId();
        $user_id = $this->getUser()->getId();
        
        $stock = $this->get('edbcontentbundle.stock');
        $items = $stock->getAllByArticleAndUser($article_id, $user_id);
        
        $data = array();
        $data['stock'] = array();
        foreach ($items as $item) {
            $data['stock'][] = array(
                'id' => $item->getId(),
                'article_id' => $article_id,
                'user_id' => $item->getUserId(),
                'name' => $item->getName(),
                'package' => $item->getPackage(),
                'quantity' => $item->getQuantity(),
                'note' => $item->getNote(),
                'storage' => $item->getStorage(),
            );
        }
        
        return $this->returnOk('get', $data);
    }


    private function putAction(Request $request, $article)
    {
        $article_id = $article->getId();
        $user_id = $this->getUser()->getId();
        $stock_id = $request->request->get('id');
        $name = $request->request->get('name');
        $quantity = $request->request->get('quantity');
        $package = $request->request->get('package');
        $note = $request->request->get('note');
        $storage = $request->request->get('storage');
        
        if (strlen($name) < 1 or strlen($package) < 1 or strlen($quantity) < 1) {
            return $this->returnError('invalid value');
        }
        
        $stock = $this->get('edbcontentbundle.stock');
        $item = null;
        
        if (intval($stock_id) > 0) {
            $item = $stock->getStockItem($article_id, $user_id, $stock_id);
            if ($item == false) {
                return $this->returnError('stock item not found');
            }
        }
        
        $item = $stock->put($item, $article_id, $user_id, $name, $quantity, $package, $note, $storage);
        
        $data = array();
        $data['stock'] = array();
        $data['stock'][] = array(
            'id' => $item->getId(),
            'article_id' => $article_id,
            'user_id' => $user_id,
            'name' => $name,
            'package' => $package,
            'quantity' => $quantity,
            'note' => $note,
            'storage' => $storage,
        );

        return $this->returnOk('get', $data);
    }


    private function deleteAction(Request $request, $article)
    {
        $data = array();

        $article_id = $article->getId();
        $user_id = $this->getUser()->getId();
        $stock_id = $request->request->get('id');
        
        $stock = $this->get('edbcontentbundle.stock');
        
        $item = $stock->getStockItem($article_id, $user_id, $stock_id);
        if ($item == false) {
            return $this->returnError('stock item not found');
        }
        
        $stock->delete($item);
        
        return $this->returnOk('get', $data);
    }
}
