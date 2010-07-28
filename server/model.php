<?php 

class DAO 
{
	protected $mysqli;
		
	public function __construct($mysqli)
	{
		$this->mysqli = $mysqli;
	}
}

class BoardDAO extends DAO
{	
	public function selectBoard($id)
	{
		$q = sprintf('SELECT * FROM boards WHERE id = %d', $id);
		if ($result = $this->mysqli->query($q))		
		{
			$board = $result->fetch_object('Board');
			$board->setPixels($this->selectBoardPixels($id));
			return $board;
		}
		else
		{
			return null;
		}
	}
	
	public function selectBoardPixels($boardId)
	{
		$collection = array();
				
		$q = sprintf('SELECT p1.id, p1.x, p1.y, p1.color, p1.created_on FROM pixels p1 LEFT JOIN pixels p2 ON p1.x = p2.x AND p1.y = p2.y AND p1.created_on < p2.created_on WHERE p2.x IS NULL AND p1.board_id = %d', $boardId);
		$result = $this->mysqli->query($q);
		
		while ($obj = $result->fetch_object('Pixel'))
			$collection[] = $obj;
			
		return $collection;
	}
	
	public function savePixel(Pixel $p)
	{
		$q = sprintf(
			"INSERT INTO pixels (x, y, color, status, board_id) VALUES(%d, %d, '%s', %d, %d)",
			$p->getX(), 
			$p->getY(), 
			$p->getColor(),
			$p->isLocked(), 
			$p->getBoardId()
		);
		if ($result = $this->mysqli->query($q))
		{
			$p->setId($this->mysqli->insert_id);
			return $p;
		}
		else {
			die($this->mysqli->error);
			return false;
		}
	}
}

class Board 
{
	private $id;
	private $size;
	private $name;
	
	private $pixels = array();
	
	public function Board()
	{
		
	}
	public function setPixels($pixels)
	{
		$this->pixels = $pixels;
	}
	
	public function toArray()
	{
		$a = array();
		$a['id'] = $this->id;
		$a['size'] = $this->size;
		$a['name'] = $this->name;
		$a['pixels'] = array();
		foreach ($this->pixels as $pixel)
		{
			$a['pixels'][$pixel->getId()] = $pixel->toArray();
		}
		return $a;
	}
}

class Pixel 
{
	const STATUS_FREE = 0;
	const STATUS_LOCKED = 1;
	
	private $id;
	private $x;
	private $y;
	private $color;
	private $status;
	private $created_on;
	private $board_id;
	
	public function Pixel()
	{
		if (isset($this->id))
			return;

		$this->id = -1;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}
		
	public function setX($x)
	{
		$this->x = $x;
	}
	
	public function getX()
	{
		return $this->x;
	}
	
	public function setY($y)
	{
		$this->y = $y;
	}
	
	public function getY()
	{
		return $this->y;
	}
	
	public function setColor($color)
	{
		$this->color = $color;
	}
	
	public function getColor()
	{
		return $this->color;
	}
	
	public function setBoardId($id)
	{
		$this->boardId = $id;
	}
	
	public function getBoardId()
	{
		return $this->boardId;
	}
	
	public function getStatus()
	{
		return $this->status;
	}
	
	public function isLocked()
	{
		return $this->status == Pixel::STATUS_LOCKED;
	}
	
	public function lock()
	{
		$this->status = Pixel::STATUS_LOCKED;
	}
	
	public function free()
	{
		$this->status = Pixel::STATUS_FREE;
	}
	
	public function toArray()
	{
		$a = array();
		$a['id'] = $this->id;
		$a['x'] = $this->x;
		$a['y'] = $this->y;
		$a['color'] = $this->color;
		
		return $a;
	}
}