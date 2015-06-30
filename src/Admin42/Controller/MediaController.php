<?php
/**
 * admin42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Admin42\Controller;

use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\View\Model\JsonModel;
use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;

class MediaController extends AbstractAdminController
{
    /**
     * @return array|mixed
     */
    public function indexAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->params("referrer") == "index") {

            return $this->getSelector('Admin42\SmartTable\Media')->getResult();
        }

        $viewModel = new ViewModel([
            'uploadForm' => $this->getForm('Admin42\Media\Upload')
        ]);

        if ($this->params('referrer') === "modal") {
            $viewModel->setTerminal(true);
        }

        return $viewModel;
    }

    public function editAction()
    {
        $editForm = $this->getForm('Admin42\Media\Edit');

        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        $media = $this->getTableGateway('Admin42\Media')->selectByPrimary((int) $this->params("id"));
        if ($prg !== false) {
            $editForm->setData($prg);
            if ($editForm->isValid()) {

                $validatedValues = $editForm->getInputFilter()->getValues();
                $media->setTitle($validatedValues['title'])
                    ->setDescription((empty($validatedValues['description']) ? null : $validatedValues['description']));

                $this->getTableGateway('Admin42\Media')->update($media);
                $this->flashMessenger()->addSuccessMessage([
                    'title' => 'toaster.media.detail.title.success',
                    'message' => 'toaster.media.detail.message.success',
                ]);

                return $this->redirect()->toRoute('admin/media/edit', ['id' => $media->getId()]);
            }
        } else {
            $editForm->setData($media->toArray());
        }

        $mediaOptions = $this->getServiceLocator()->get('Admin42\MediaOptions');

        return [
            'editForm' => $editForm,
            'media' => $media,
            'dimensions' => $mediaOptions->getDimensions(false)
        ];
    }

    public function cropAction()
    {
        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);

        $cmd = $this->getCommand('Admin42\Media\ImageCrop')
            ->setMediaId($this->params("id"))
            ->setBoxWidth($data['width'])
            ->setBoxHeight($data['height'])
            ->setOffsetX($data['x'])
            ->setOffsetY($data['y'])
            ->setDimensionName($this->params("dimension"));
        $cmd->run();
        if ($cmd->hasErrors()) {
            return new JsonModel(['success' => false]);
        }

        return new JsonModel(['success' => true]);
    }

    /**
     * @return \Zend\Http\Response\Stream
     */
    public function streamAction()
    {
        return $this->getCommand('Admin42\Media\Stream')
            ->setMediaId($this->params("id"))
            ->setDimension($this->params("dimension"))
            ->run();
    }

    /**
     * @return JsonModel
     * @throws \Exception
     */
    public function uploadAction()
    {
        $jsonModel = new JsonModel();
        $form = $this->getForm('Admin42\Media\Upload');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()) {
                $this->getFormCommand()
                    ->setCommand($this->getCommand('Admin42\Media\Upload'))
                    ->setForm($form)
                    ->enableAutomaticFormFill(false)
                    ->run();
                $jsonModel->answer = "File transfer completed";
            }
        }

        return $jsonModel;
    }
}