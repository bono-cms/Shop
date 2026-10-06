<?php

/**
 * This file is part of the Bono CMS
 * 
 * For the full copyright and license information, please view
 * the license file that was distributed with this source code.
 */

namespace Shop\Controller\Admin;

use Cms\Controller\Admin\AbstractController;
use Krystal\Stdlib\VirtualEntity;

final class AttributeValue extends AbstractController
{
    /**
     * Renders the form
     * 
     * @param \Krystal\Stdlib\VirtualEntity|array $value
     * @param string $title Page title
     * @return string
     */
    private function createForm($value, $title)
    {
        $new = is_object($value);

        // Append breadcrumbs
        $this->view->getBreadcrumbBag()
                   ->addOne('Shop', 'Shop:Admin:Browser@indexAction')
                   ->addOne('Attributes', 'Shop:Admin:Attributes@indexAction')
                   ->addOne($title);

        return $this->view->render('attributes/value', [
            'groups' => $this->getModuleService('attributeGroupManager')->fetchList(),
            'value' => $value,
            'new' => $new
        ]);
    }

    /**
     * Deletes attribute value by its associated id
     * 
     * @param string $id
     * @return string
     */
    public function deleteAction($id)
    {
        $service = $this->getModuleService('attributeValueManager');
        $service->deleteById($id);

        $this->flashBag->set('success', 'Selected element has been removed successfully');

        return $this->json([
            'refresh' => true
        ]);
    }

    /**
     * Renders adding form
     *
     * @return string
     */
    public function addAction()
    {
        return $this->createForm(new VirtualEntity(), 'Add attribute');
    }

    /**
     * Renders edit form
     * 
     * @param string $id
     * @return string
     */
    public function editAction($id)
    {
        $value = $this->getModuleService('attributeValueManager')->fetchById($id, true);

        if ($value !== false) {
            $name = $this->getCurrentProperty($value, 'name');
            return $this->createForm($value, $this->translator->translate('Edit the attribute "%s"', $name));
        } else {
            return false;
        }
    }

    /**
     * Save the record
     * 
     * @return string
     */
    public function saveAction()
    {
        $validator = $this->createValidation();

        $validator->field('value.group_id')
                  ->required();

        $validator->field('translation.*.name')
                  ->required()
                  ->addRule('minlength', null, ['min' => 2]);

        if ($validator->isPassed()) {
            $input = $this->request->getPost('value');

            $service = $this->getModuleService('attributeValueManager');
            $service->save($this->request->getPost());

            if (!empty($input['id'])) {
                $this->flashBag->set('success', 'The element has been updated successfully');

                return $this->json([
                    'refresh' => true
                ]);

            } else {
                $this->flashBag->set('success', 'The element has been created successfully');

                return $this->json([
                    'redirect' => $this->createUrl('Shop:Admin:AttributeValue@editAction', [$service->getLastId()]),
                ]);
            }

        } else {
            return $this->json([
                'errors' => $validator->getErrors()
            ]);
        }
    }
}
