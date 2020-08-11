<?php

namespace App\Twig;

use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;

class PaginationExtension extends \Twig_Extension
{
    /**
     * @var Processor
     */
    protected $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('js_sortable_render', array($this, 'JSSortable'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('pagination_sortable', array($this, 'sortable'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('pagination_info', array($this, 'paginationInfo'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('js_pagination_render', array($this, 'JSPagination'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('search_pagination_render', array($this, 'searchPagination'), array('is_safe' => array('html'), 'needs_environment' => true))
        );
    }

    /**
     * Renders the sortable template
     *
     * @param \Twig_Environment $env
     * @param SlidingPagination $pagination
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function paginationInfo(\Twig_Environment $env, SlidingPagination $pagination)
    {
        $paginatorData = $pagination->getPaginationData();
        return $env->render("_shared_components/pagination/paginator_info.html.twig", [
              'start' => (($paginatorData['numItemsPerPage'] * $paginatorData['current']) - $paginatorData['numItemsPerPage']) + 1,
              'end' => ($paginatorData['numItemsPerPage'] * $paginatorData['current']),
              'total' => $paginatorData['totalCount']
        ]);
    }

    /**
     * Create a sort url for the field named $title
     * and identified by $key which consists of
     * alias and field. $options holds all link
     * parameters like "alt, class" and so on.
     *
     * $key example: "title"
     *
     * @param \Twig_Environment $env
     * @param SlidingPagination $pagination
     * @param string $title
     * @param string $key
     * @param array $options
     * @param array $params
     * @param string $template
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sortable(\Twig_Environment $env, SlidingPagination $pagination, $title, $key, $options = array(), $params = array(), $template = null)
    {
        $paginatorOptions = $pagination->getPaginatorOptions();

        if (strpos($key, '.') === false) {
            $key = sprintf('%s.%s', $paginatorOptions['ENTITY_SHORT_NAME'], $key);
        }

        return $env->render(
            $template ?: $pagination->getSortableTemplate(),
            $this->processor->sortable($pagination, $title, $key, $options, $params)
        );
    }

    /**
     * Renders the sortable template
     *
     * @param \Twig_Environment $env
     * @param SlidingPagination $pagination
     * @param string $title
     * @param string $field
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function JSSortable(\Twig_Environment $env, SlidingPagination $pagination, string $title, string $field)
    {
        $paginatorOptions = $pagination->getPaginatorOptions();

        if (strpos($field, '.') === false) {
            $field = sprintf('%s.%s', $paginatorOptions['ENTITY_SHORT_NAME'], $field);
        }

        return $env->render("_shared_components/pagination/sortable_link.html.twig", [
              'options' => ['field' => $field, 'direction' => 'asc'],
              'title' => $title
        ]);
    }

    /**
     * Renders the pagination template
     *
     * @param \Twig_Environment $env
     * @param SlidingPagination $pagination
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function JSPagination(\Twig_Environment $env, SlidingPagination $pagination)
    {
        $paginatorOptions = $pagination->getPaginatorOptions();
        $paginatorData = $pagination->getPaginationData();

        return $env->render("_shared_components/pagination/paginator_js.html.twig", [
              'page_parameter_name' => $paginatorOptions['pageParameterName'],
              'route' => $pagination->getRoute(),
              'params' => $pagination->getParams(),
              'leaps' => true,
              'max_visible' => $paginatorData['pageRange'],
              'page' => $paginatorData['current'],
              'total_page' => $paginatorData['pageCount']
        ]);
    }

    /**
     * Renders the pagination template
     *
     * @param \Twig_Environment $env
     * @param SlidingPagination $pagination
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function searchPagination(\Twig_Environment $env, SlidingPagination $pagination)
    {
        return $env->render("_shared_components/pagination/search_form.html.twig", [
              'route' => $pagination->getRoute(),
              'disabled' => $pagination->getTotalItemCount() === 0 ? true : false,
        ]);
    }
}
