<?php
class LinkField extends TextField{

	/**
	 * @var Boolean
	 **/
	protected $isFrontend = false;

	/**
	 * @var Link
	 **/
	protected $linkObject;


	public static $allowed_actions = array(
		'LinkForm',
		'LinkFormHTML',
		'doSaveLink'
	);


	public function Field($properties = array()){
		Requirements::javascript('linkfield/javascript/linkfield.js');
		return parent::Field();
	}

	/**
	 * The LinkForm for the dialog window
	 *
	 * @return Form
	 **/
	public function LinkForm(){
		$link = $this->getLinkObject();
		$action = FormAction::create('doSaveLink', 'Save')->setUseButtonTag('true');

		if(!$this->isFrontend){
			$action->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept');
		}

		$fields = singleton('Link')->getCMSFields();
		
		$title = $link ? 'Edit Link' : 'Add Link';
		$fields->insertBefore(HeaderField::create('LinkHeader', $title), 'Title');
		$actions = FieldList::create($action);
		$form = Form::create($this, 'LinkForm', $fields, $actions);

		if($link){
			$form->loadDataFrom($link);
			$fields->push(HiddenField::create('LinkID', 'LinkID', $link->ID));
		}

		$this->owner->extend('updateLinkForm', $form);

		return $form;
	}


	/**
	 * Either updates the current link or creates a new one
	 * Returns field template to update the interface
	 * @return String
	 **/
	public function doSaveLink($data, $form){
		$link = $this->getLinkObject() ? $this->getLinkObject() : Link::create();
		$link->update($data);
		$link->write();
		$this->setValue($link->ID);
		$this->setForm($form);
		return $this->FieldHolder();
	}

	
	/**
	 * Returns the current link object
	 *
	 * @return Link
	 **/
	public function getLinkObject(){
		if(!$this->linkObject){
			$id = $this->Value() ? $this->Value() : Controller::curr()->request->requestVar('LinkID');
			if((int)$id){
				$this->linkObject = Link::get()->byID($id);
			}		
		}
		return $this->linkObject;
	}


	/**
	 * Returns the HTML of the LinkForm for the dialog
	 *
	 * @return String
	 **/
	public function LinkFormHTML(){
		return $this->LinkForm()->forTemplate();
	}


	public function getIsFrontend(){
		return $this->isFrontend;
	}


	public function setIsFrontend($bool){
		$this->isFrontend = $bool;
		return $this->this;
	}
}