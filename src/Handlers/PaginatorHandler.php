<?php

namespace App\Handlers;

/*
 * Helper functions for building a server-side processing SQL query
 *
 * The static functions in this class are just helper functions to help build
 * the SQL used in the DataTables demo server-side processing scripts. These
 * functions obviously do not represent all that can be done with server-side
 * processing, they are intentionally simple to show how it works. More complex
 * server-side processing operations will likely require a custom script.
 */


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\Mapping\ClassMetadata;

use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PaginatorHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * The class metadata for $entityShortName.
     *
     * @var string
     */
    private $entityShortName;

    /**
     * All fields of the entity.
     *
     * @var array
     */
    private $columns;
    private $joinColumns;
    private $extraColumns;

    /**
     * Results pre page
     *
     * @var string
     */
    private $itemsPerPage;

    //-------------------------------------------------
    // Ctor. && Init column arrays
    //-------------------------------------------------

    /**
     * PaginatorHandler constructor.
     *
     *
     * @var EntityManagerInterface $em
     * @var PaginatorInterface $paginator
     * @param int $adminPaginatorItemPerPage
     */
    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator, int $adminPaginatorItemPerPage)
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->itemsPerPage = $adminPaginatorItemPerPage;
    }

    /**
     * Searching / Filtering.
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * @param QueryBuilder $qb
     *
     * @param array $requestParams
     * @return QueryBuilder
     */
    private function setWhere(QueryBuilder $qb, array $requestParams)
    {
        // global filtering
        if (isset($requestParams['search']) && '' != $requestParams['search']) {
            $globalSearch = $this->cleanInput($requestParams['search']);
            foreach ($this->columns as $column) {
                $qb->orwhere($qb->expr()->like(sprintf("%s.%s", $this->entityShortName, $column), ":globalSearch"));
            }

            $qb->setParameter("globalSearch", "%{$globalSearch}%");
        }

        // individual filtering
        if (isset($requestParams['filter']) && is_array($requestParams['filter'])) {
            $columns = array_merge(array_keys($this->joinColumns), $this->columns);
            foreach ($requestParams['filter'] as $fieldName => $data) {
                $content = $this->cleanInput($data);
                if (empty($content) && $content !== "0") {
                    continue;
                }
                
                // date filtering
                if (in_array($fieldName, $columns) && is_string($content) && (preg_match("/^\d{2}\/\d{2}\/\d{4} - \d{2}\/\d{2}\/\d{4}$/", $content) || preg_match("/^\d{2}\/\d{2}\/\d{4}\ \d{2}\:\d{2}\:\d{2}\ - \d{2}\/\d{2}\/\d{4} \d{2}\:\d{2}\:\d{2}\$/", $content))) {
                    list($start, $end) = explode(' - ', $content);
                    $qb->andWhere($qb->expr()->between(sprintf("%s.%s", $this->entityShortName, $fieldName), ":{$fieldName}_start", ":{$fieldName}_end"));
                    $qb->setParameter(":{$fieldName}_start", new \DateTime($start));
                    $qb->setParameter(":{$fieldName}_end", new \DateTime($end));
                } elseif (in_array($fieldName, $columns) && is_array($content)) {
                    $qb->andWhere($qb->expr()->in(sprintf("%s.%s", $this->entityShortName, $fieldName), ":{$fieldName}"));
                    $qb->setParameter(":{$fieldName}", $content);
                } elseif (in_array($fieldName, $columns) && is_numeric($content)) {
                    $qb->andWhere($qb->expr()->eq(sprintf("%s.%s", $this->entityShortName, $fieldName), ":{$fieldName}"));
                    $qb->setParameter(":{$fieldName}", $content);
                } elseif (in_array($fieldName, $columns) && is_string($content) && preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $content)) {
                    $qb->andWhere($qb->expr()->like(sprintf("%s.%s", $this->entityShortName, $fieldName), ":{$fieldName}"));
                    $datetime = date('Y-m-d', strtotime(str_replace('/', '-', $content)));
                    // $datetime = new \DateTime($content);
                    // $datetime->format('Y-m-d');
                    $qb->setParameter(":{$fieldName}", $datetime);
                } elseif (in_array($fieldName, $columns) && is_string($content) && !preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $content)) {
                    $qb->andWhere($qb->expr()->like(sprintf("%s.%s", $this->entityShortName, $fieldName), ":{$fieldName}"));
                    $qb->setParameter(":{$fieldName}", "%{$content}%");
                }

            }
        }

        return $qb;
    }

    /**
     * Set select from.
     *
     * @param QueryBuilder $qb
     *
     * @param array $extraColumns
     * @return QueryBuilder
     */
    private function setSelectFrom(QueryBuilder $qb, array $extraColumns = array())
    {
        $qb->select(sprintf('partial %s.{%s}', $this->entityShortName, implode(', ', $this->columns)));
        foreach ($extraColumns as $column) {
            if (strpos($column, ".") !== FALSE) {
                list($columnShortName, $columnName) = explode(".", $column);
                $qb->addSelect($columnName);
                $qb->leftJoin($column, $columnName);
                continue;
            }
            $qb->addSelect($column);
            $qb->leftJoin(sprintf('%s.%s', $this->entityShortName, $column), $column);
        }
        return $qb;
    }

    /**
     * Constructs a Query instance.
     *
     * @param string $entityName
     * @param array $requestParams
     * @param array $extraColumns
     * @return Query
     *
     * @throws \Exception
     */
    public function execute(string $entityName, array $requestParams, array $extraColumns = array())
    {
    	/** @var ClassMetadata $metadata */
        $metadata = $this->getMetadata($entityName);
        
        $this->columns = $metadata->getFieldNames();
        $this->joinColumns = $metadata->getAssociationMappings();
        $this->extraColumns = $extraColumns;
        $this->entityShortName = $this->setEntityShortName($metadata);
        $qb = $this->em->createQueryBuilder()->from($entityName, $this->entityShortName);
        $qb = $this->setSelectFrom($qb, $extraColumns);
        $qb = $this->setWhere($qb, $requestParams);

        return $qb->getQuery();
    }

    /**
     * Clean up the input
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function cleanInput($data)
    {
        return is_array($data) ? $data : htmlentities(htmlspecialchars(stripslashes(trim($data))), ENT_QUOTES | ENT_IGNORE, "UTF-8");
    }

	/**
	 * @param array $requestParams
	 * @param string $entityName
	 * @param array $extraColumns
	 * @param array $options
	 *
	 * @return \Knp\Component\Pager\Pagination\PaginationInterface
	 * @throws \Exception
	 */
    public function dynamicPaginatorHandler(?array $requestParams, string $entityName, array $extraColumns = array(), array $options = array())
    {
        return $this->paginator->paginate(
            $this->execute($entityName, $requestParams, $extraColumns),
            $requestParams['page'] ?? 1,
            $this->itemsPerPage,
            array_merge(
            	['ENTITY_SHORT_NAME' => $this->entityShortName, 'COLUMNS' => $this->columns],
	            [PaginatorInterface::DEFAULT_SORT_FIELD_NAME => sprintf('%s.%s', $this->entityShortName, $options['DEFAULT_SORT_FIELD_NAME'] ?? 'id'), PaginatorInterface::DEFAULT_SORT_DIRECTION => 'desc'],
	            $options
            )
        );
    }

    /**
     * @param array|null $requestParams
     * @param Query $query
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function paginatorHandler(?array $requestParams, Query $query)
    {
        return $this->paginator->paginate(
            $query,
            $requestParams['page'] ?? 1,
            $this->itemsPerPage
        );
    }

    /**
     * Internal methods
     */

    /**
     * Get metadata.
     *
     * @param string $entityName
     *
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     * @throws \BadFunctionCallException
     */
    private function getMetadata(string $entityName)
    {
        try {
            $metadata = $this->em->getMetadataFactory()->getMetadataFor($entityName);
        } catch (\Exception $e) {
            throw new \BadFunctionCallException('DatatableQueryBuilder::getMetadata(): Given object ' . $entityName . ' is not a Doctrine Entity.');
        }
        return $metadata;
    }

    /**
     * Get entity short name.
     *
     * @param ClassMetadata $metadata
     *
     * @return string
     */
    private function setEntityShortName(ClassMetadata $metadata): string
    {
        return '_' . strtolower($metadata->getReflectionClass()->getShortName());
    }

    /**
     * Get the value of The class metadata for $entityShortName.
     *
     * @return string
     */
    public function getEntityShortName(): ?string
    {
        return $this->entityShortName;
    }

    /**
     * Check that the text containe valid date.
     *
     * @param null|string $date
     * @param string $format
     * @return bool
     */
    public function isValidDate(?string $date, string $format = 'm/d/Y')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Change items per page.
     *
     * @param int $itemsPerPage
     * @return mixed
     */
    public function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage;
    }
}
