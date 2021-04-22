<?php

namespace flexycms\FlexyCacheBundle\Controller;


use flexycms\FlexyCacheBundle\Service\CacheService;
use flexycms\BreadcrumbsBundle\Utils\Breadcrumbs;
use flexycms\FlexyAdminFrameBundle\Controller\AdminBaseController;
use Symfony\Component\Routing\Annotation\Route;

class AdminCacheController extends AdminBaseController
{
    private $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @Route("/admin/cache", name="admin_cache")
     */
    public function index()
    {
        $forRender = parent::renderDefault();
        $forRender['title'] = "Управление кэшем";

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->prepend($this->generateUrl("admin_filemanager"), 'Управление кэшем');
        $breadcrumbs->prepend($this->generateUrl("admin_home"), 'Главная');
        $forRender['breadcrumbs'] = $breadcrumbs;

        $forRender['cacheSize'] = $this->cacheService->getSizeString(1);

        return $this->render('@FlexyCache/cache.html.twig', $forRender);
    }

    /**
     * @Route("/admin/cache/clear", name="admin_cacheclear")
     */
    public function clear()
    {
        $this->cacheService->clear();
        return $this->redirectToRoute("admin_cache");
    }


}