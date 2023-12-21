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
        $this->all_texts = \TestPlugin\Test::findBySQL("1");

        //$this->all_texts = \TestPlugin\Test::findBySQL("1");
    }

    public function edit_text_action(string $text_id = '')
    {
        PageLayout::setTitle('Edit text');
        $this->text_obj = \TestPlugin\Test::find($text_id);
        if (!$this->text_obj) {
            $this->text_obj = new \TestPlugin\Test();
        }
    }

    public function store_text_action(string $text_id = '')
    {
        CSRFProtection::verifyRequest();
        $this->text_obj = \TestPlugin\Test::find($text_id);
        if (!$this->text_obj) {
            $this->text_obj = new \TestPlugin\Test();
            $this->text_obj->author_id = $GLOBALS['user']->id;
        }
        $this->text_obj->setData([
            'title' => Request::get('title'),
            'description' => Request::get('description'),
            'type' => Request::int('type')
        ]);
        if ($this->text_obj->store() !== false) {
            PageLayout::postSuccess('The text was
successfully saved');
        } else {
            PageLayout::postError('An error occurred while
saving the text');
        }
        $this->redirect('overview/index');
    }
}
