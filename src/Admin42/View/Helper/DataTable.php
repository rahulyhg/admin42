<?php
namespace Admin42\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;

class DataTable extends AbstractHelper
{
    /**
     * @var \Admin42\DataTable\DataTable
     */
    protected $dataTable;

    /**
     * @var string[]
     */
    protected $dataTableMap = array();

    /**
     * @var string
     */
    protected $partial = 'partial/admin42/datatable-table';

    /**
     * @param \Admin42\DataTable\DataTable $dataTable
     * @return $this
     */
    public function __invoke(\Admin42\DataTable\DataTable $dataTable = null, $partial = null)
    {
        if ($dataTable !== null) {
            $this->setDataTable($dataTable);
        }

        if ($partial !== null) {
            $this->setPartial($partial);
        }

        return $this;
    }

    /**
     * @param \Admin42\DataTable\DataTable $dataTable
     * @return $this
     */
    public function setDataTable(\Admin42\DataTable\DataTable $dataTable)
    {
        $this->dataTable = $dataTable;

        $hash = spl_object_hash($dataTable);
        if (!array_key_exists($hash, $this->dataTableMap)) {
            $this->dataTableMap[$hash] = 'dt_' . substr(md5(uniqid()), 0, 10);
        }

        return $this;
    }

    /**
     * @param string $partial
     * @return $this
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $hash = spl_object_hash($this->dataTable);

        return $this->dataTableMap[$hash];
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->dataTable->applyDecorators();

        $model = array(
            'dataTable' => $this->dataTable,
            'name' => $this->getName(),
        );

        return $this->view->render($this->partial, $model);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}