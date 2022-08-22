<?php
namespace Magenest\GoogleShopping\Model\CategoryMapping;

class ReaderMapper
{
    /**
     * @var array
     */
    protected $multiplicityArray = [];

    /**
     * @var string
     */
    protected $mappingDelimiter = ' > ';

    /**
     * @param string $search
     *
     * @return array
     */
    public function getData(string $search)
    {
        $data = [];
        $result = [];
        foreach ($this->multiplicityArray as $multiplicity) {
            $items = $multiplicity->getItems();
            foreach ($items as $item) {
                $item->setMappingDelimiter($this->mappingDelimiter);
                $data = array_merge($data, $item->getRows($search));
            }
        }

        foreach ($data as $path => $row) {
            $result[] = [
                'file'  => $row,
                'path'  => $path,
                'label' => $path,
                'id'    => $path,
            ];
        }

        return $result;
    }

    /**
     * @param $multiplicity
     * @return $this
     */
    public function addMultiplicity($multiplicity)
    {
        $this->multiplicityArray[] = $multiplicity;
        return $this;
    }
}
