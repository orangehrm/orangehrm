  public function executeNew(sfWebRequest $request)
  {
    $this->form = new <?php echo $this->getModelClass().'Form' ?>();
  }
