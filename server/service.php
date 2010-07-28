<?php

include('model.php');

class Service 
{
	private $dao;
	
	public function __construct()
	{
		$mysqli = new mysqli('localhost', 'pixels', 'P1x3lz','pixels');
		$this->dao = new BoardDAO($mysqli);
	}
	
	public function getBoard($id)
	{
		$board = $this->dao->selectBoard($id);
		return json_encode(array('action'=>'board', 'data'=>$board->toArray()));
	}
	
	public function insert($x, $y, $color, $boardId)
	{
		$pixel = new Pixel();
		$pixel->setX($x);
		$pixel->setY($y);
		$pixel->setColor($color);
		$pixel->setBoardId($boardId);
		
		$pixel = $this->dao->savePixel($pixel);
		return json_encode(array('action'=>'pixel', 'data'=>$pixel->toArray()));
	}
}