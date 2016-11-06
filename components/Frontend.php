<?php

namespace infoweb\cms\components;

use infoweb\alias\models\Alias;
use Yii;
use yii\base\Component;

use infoweb\pages\models\Page;
use infoweb\menu\models\MenuItem;
use yii\web\NotFoundHttpException;

class Frontend extends Component {

    protected $menuItem = null;
    protected $page = null;
    protected $parentMenuItem = null;
    protected $parentPage = null;
    /**
     * @var Page
     */
    protected $homePage = null;

    /**
     * Returns a page, based on the alias that is provided in the request or,
     * if no alias is provided, the homepage
     *
     * @return  Page
     */
    public function getActivePage()
    {
        if ($this->page === null) {
            // An alias is provided
            if ($alias = Yii::$app->request->get('alias')) {
                // Load the alias translation
                $aliasLang = Alias::findOne([
                    'url' => $alias,
                    'language' => Yii::$app->language
                ]);

                if (!$aliasLang) {
                    throw new NotFoundHttpException('Page by url "' . $alias . '" is not found');
                }

                // Get the alias
                //            $alias = $aliasLang->alias;

                // Get the page
                $page = $aliasLang->entityModel;

                // The page must be active
                if ($page->active != 1) {
                    throw new NotFoundHttpException('Page by url "' . $alias . '" is not found');
                }

                $this->page = $page;
            } else {
                // Load the page that is marked as the 'homepage'
                $this->page = $this->getHomePage();
            }
        }

        return $this->page;
    }

    /**
     * Get the active parent page
     *
     * @return Page
     */
    public function getActiveParentPage()
    {
        if (isset($this->activeParentMenuItem->entity_id)) {
            return Page::findOne(['id' => $this->parentMenuItem->entity_id, 'active' => 1]);
        }
    }

    /**
     * Get the active menu item
     *
     * @return MenuItem
     */
    public function getActiveMenuItem()
    {
        if ($this->menuItem === null) {
            $this->menuItem = MenuItem::findOne([
                'entity' => Page::className(),
                'entity_id' => $this->page->id,
                'active' => 1,
                'menu_id' => 3, //@todo Set correnct menu id?
            ]);
        }

        return $this->menuItem;
    }

    /**
     * Get the active parent menu item
     *
     * @return MenuItem
     */
    public function getActiveParentMenuItem()
    {
        if ($this->parentMenuItem === null) {
            $menuItem = $this->activeMenuItem->parent;

            if ($menuItem && $menuItem->active = 1) {
                $this->parentMenuItem = $menuItem;
            } else {
                $this->parentMenuItem = new MenuItem;
            }
        }

        return $this->parentMenuItem;
    }

    public function getActiveNavigationPages() {
        $homePage = $this->getHomePage();
        $pages = [];
        if ($homePage) {
            $page = $this->getNavigationPageParams($homePage);
            $pages[] = $page;
        }

        $activePage = $this->getActivePage();
        if ($activePage && (!$homePage || $activePage->id !== $homePage->id)) {
            $activeParentPage = $this->getActiveParentPage();
            if ($activeParentPage && (!$homePage || $activeParentPage->id !== $homePage->id)) {
                $activeParentPage = $this->getNavigationPageParams($activeParentPage);

                $pages[] = $activeParentPage;
            }

            $page = $this->getNavigationPageParams($activePage);

            $pages[] = $page;
        }

        return $pages;
    }

    public function getHomePage() {
        if ($this->homePage === null) {
            $this->homePage = Page::find()->where('homepage=1')->one();
        }

        return $this->homePage;
    }

    /**
     * @param $activePage
     * @param $activePageTranslate
     * @param $activePageSeoTranslate
     * @return array
     */
    protected function getNavigationPageParams($activePage): array
    {
        $activePageTranslate = $activePage->translate();
        $activePageSeoTranslate = $activePage->seo->translate();

        $page = [
            'url' => $activePage->getUrl(false),
            'text' => $activePageTranslate->content,
            'name' => $activePageTranslate->name,
            'title' => $activePageSeoTranslate->title,
            'header' => $activePageTranslate->name,
            'keywords' => $activePageSeoTranslate->keywords,
            'description' => $activePageSeoTranslate->description,
        ];
        return $page;
    }
}