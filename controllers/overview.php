<?php
class OverviewController extends \TestPlugin\Controller
{
    private function buildSidebar()
    {
        $sidebar = Sidebar::Get();

        $actionWidget = $sidebar->addWidget(new ActionsWidget());
        $actionWidget->addLink(
            'Create text',
            $this->url_for('overview/edit_text'),
            Icon::create('add'),
            ['data-dialog' => true]
        );
    }

    public function index_action()
    {
        Navigation::activateItem('test_root/test_overview');
        PageLayout::setTitle('Texts overview');
        OverviewController::buildSidebar();

        //$this->all_texts = \TestPlugin\Test::findBySQL("1");
    }
}
