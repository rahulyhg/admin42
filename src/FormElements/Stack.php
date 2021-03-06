<?php

/*
 * admin42
 *
 * @package admin42
 * @link https://github.com/kiwi-suite/admin42
 * @copyright Copyright (c) 2010 - 2017 kiwi suite (https://www.kiwi-suite.com)
 * @license MIT License
 * @author kiwi suite <tech@kiwi-suite.com>
 */


namespace Admin42\FormElements;

use Ramsey\Uuid\Uuid;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;

class Stack extends Fieldset
{
    /**
     * @var FieldsetInterface[]
     */
    protected $protoTypes = [];

    /**
     * @var array
     */
    protected $initialElements = [];

    /**
     * @var bool
     */
    protected $shouldCreateChildrenOnPrepareElement = true;

    /**
     * Checks if the object can be set in this fieldset
     *
     * @param object $object
     * @return bool
     */
    public function allowObjectBinding($object)
    {
        return true;
    }

    /**
     * Checks if this fieldset can bind data
     *
     * @return bool
     */
    public function allowValueBinding()
    {
        return true;
    }

    /**
     * @param array|\Traversable $options
     * @return void|\Zend\Form\Element|\Zend\Form\ElementInterface
     */
    public function setOptions($options)
    {
        if (!empty($options['sets']) && \is_array($options['sets'])) {
            $this->handleSets($options['sets']);
        }

        return $this;
    }

    /**
     * @param array $sets
     */
    protected function handleSets(array $sets)
    {
        foreach ($sets as $spec) {
            $element = $this->getFormFactory()->createFieldset($spec);
            $this->addProtoType($element->getName(), $element);
        }
    }

    /**
     * @param array $initialElements
     */
    public function setInitialElements(array $initialElements)
    {
        $this->initialElements = $initialElements;
    }

    /**
     * @param FormInterface $form
     * @return void
     */
    public function prepareElement(FormInterface $form)
    {
        if ($this->shouldCreateChildrenOnPrepareElement === true) {
            if (\count($this->initialElements) > 0) {
                $fieldsetName = Uuid::uuid4()->toString();
                for ($i = 0; $i < \count($this->initialElements); $i++) {
                    $this->attachFieldset($this->initialElements[$i], $fieldsetName . '-' . $i);
                }
            }
        }

        parent::prepareElement($form);

        $name = $this->getName();
        foreach ($this->getProtoTypes() as $fieldset) {
            if ($fieldset->getOption('originalName') === null) {
                $fieldset->setOption('originalName', $fieldset->getName());
            }
            $fieldset->setOption('formServiceHash', $form->getOption('formServiceHash'));
            $fieldset->setName($name . '[' . $fieldset->getName() . ']');

            if ($fieldset instanceof ElementPrepareAwareInterface) {
                $fieldset->prepareElement($form);
            }
        }
    }

    /**
     * @param array|\Traversable $data
     * @throws \Exception
     */
    public function populateValues($data)
    {
        if (!\is_array($data) && !$data instanceof \Traversable) {
            throw new \Exception(\sprintf(
                '%s expects an array or Traversable set of data; received "%s"',
                __METHOD__,
                (\is_object($data) ? \get_class($data) : \gettype($data))
            ));
        }

        $this->shouldCreateChildrenOnPrepareElement = false;
        \uasort($data, function ($value1, $value2) {
            if (!\array_key_exists('__index__', $value1) || !\array_key_exists('__index__', $value2)) {
                return 0;
            }
            if ($value1['__index__'] == $value2['__index__']) {
                return 0;
            }

            return ($value1['__index__'] < $value2['__index__']) ? -1 : 1;
        });

        foreach ($data as $key => $value) {
            if (!\array_key_exists('__type__', $value)) {
                throw new \Exception(\sprintf(
                    '%s expects array items with an attribute "__type__"',
                    __METHOD__
                ));
            }
            if (\array_key_exists('__deleted__', $value) && $value['__deleted__'] == 'true') {
                continue;
            }

            $fieldset = $this->attachFieldset($value['__type__'], $key);

            if ($fieldset === false) {
                continue;
            }

            if ($fieldset instanceof FieldsetInterface) {
                $fieldset->populateValues($value);
            } else {
                $fieldset->setValue($value);
            }
        }
    }

    /**
     * @param $type
     * @param $name
     * @return FieldsetInterface|false
     */
    public function attachFieldset($type, $name)
    {
        $this->shouldCreateChildrenOnPrepareElement = false;

        if (!isset($this->protoTypes[$type])) {
            return false;
        }

        $fieldset = clone $this->protoTypes[$type];
        $fieldset->setName($name);

        $this->add($fieldset);

        return $fieldset;
    }

    /**
     * @param string $type
     * @param FieldsetInterface|array $fieldset
     * @throws \Exception
     * @return array|\Zend\Form\ElementInterface|FieldsetInterface
     */
    public function addProtoType($type, $fieldset)
    {
        $factory = $this->getFormFactory();
        if (\is_array($fieldset) || ($fieldset instanceof \Traversable && !$fieldset instanceof FieldsetInterface)) {
            $fieldset = $factory->createFieldset($fieldset);
        }

        if (!$fieldset instanceof FieldsetInterface) {
            throw new \Exception(\sprintf(
                '%s requires that $fieldset be an object implementing %s; received "%s"',
                __METHOD__,
                FieldsetInterface::class,
                (\is_object($fieldset) ? \get_class($fieldset) : \gettype($fieldset))
            ));
        }

        $fieldset->setOption('stackType', $type);

        $fieldset->add(
            $factory->create(['name' => '__index__', 'type' => Hidden::class])
        );

        $fieldset->add(
            $factory->create(['name' => '__type__', 'type' => Hidden::class])
        );

        $fieldset->add(
            $factory->create(['name' => '__name__', 'type' => Hidden::class])
        );

        $fieldset->add(
            $factory->create(['name' => '__deleted__', 'type' => Hidden::class])
        );

        $this->protoTypes[$type] = $fieldset;

        return $fieldset;
    }

    /**
     * @return \Zend\Form\FieldsetInterface[]
     */
    public function getProtoTypes()
    {
        return $this->protoTypes;
    }

    /**
     * @param array $values
     * @return array
     */
    public function bindValues(array $values = [])
    {
        $stack = [];
        foreach ($values as $name => $value) {
            if (!isset($value['__deleted__']) || $value['__deleted__'] == 'true') {
                continue;
            }
            $element = $this->get($name);

            if ($element instanceof FieldsetInterface) {
                $stack[$name] = $element->bindValues($value);
            } else {
                $stack[$name] = $value;
            }
        }

        return $stack;
    }
}
