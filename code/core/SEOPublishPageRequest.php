<?php

class SEOPublishPageRequest extends GridFieldDetailForm_ItemRequest
{
    private static $allowed_actions = array('ItemEditForm');

    function ItemEditForm()
    {
        $form = parent::ItemEditForm();

        $actions = $form->Actions();

        $actions->removeByName('action_doSave');
        $actions->removeByName('action_doDelete');

        $button = FormAction::create('doPublish');
        $button->setTitle('Save Live Page');
        $button->addExtraClass('ss-ui-action-constructive ui-button-text-icon-primary');
        $actions->push($button);

        $form->setActions($actions);

        return $form;
    }
    
    public function doPublish($data, $form)
    {
        $page = DataObject::get_by_id($this->record->ClassName, $this->record->ID);

        if($page == NULL){
            $page = Versioned::get_by_stage($this->record->ClassName, 'Stage')->byID($this->record->ID);
        }
        $form->saveInto($page);
        $page->write();
        $page->writeToStage('Stage');
        $page->doPublish();

        $form->sessionMessage('Updated live page', 'good');

        Controller::curr()->redirectBack();
    }
}