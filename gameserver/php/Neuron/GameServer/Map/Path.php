<?php
class Neuron_GameServer_Map_Path
{
	private $path;
	private $cost;
	
	private $dx = 0;
	private $dy = 0;
	private $dz = 0;

	public function __construct ($path, $cost)
	{
		$this->path = $path;
		$this->cost = $cost;
	}
	
	public function getCost ()
	{
		return $this->cost;
	}
	
	public function transform ($x, $y, $z = 0)
	{
		$this->dx = $x;
		$this->dy = $y;
		$this->dz = $z;
	}
	
	public function getPath ()
	{
		$out = array ();
		
		foreach ($this->path as $v)
		{
			$out[] = new Neuron_GameServer_Map_Location ($v[0] + $this->dx, $v[1] + $this->dy);
		}
	
		return $out;
	}
}
