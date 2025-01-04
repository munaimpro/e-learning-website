<?php

namespace BitCode\BitForm\Admin\Form\Template;

class TemplateBase
{
  protected $title;
  protected $description;
  protected $status;
  protected $thumbnail;
  protected $category;

  public function getTitle()
  {
    return $this->title;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function getThumbnail()
  {
    return $this->thumbnail;
  }

  public function getCategory()
  {
    return $this->category;
  }

  public function getLayout($newFormId)
  {
    return $this->layout($newFormId);
  }

  public function getFields($newFormId)
  {
    return $this->fields($newFormId);
  }
}
