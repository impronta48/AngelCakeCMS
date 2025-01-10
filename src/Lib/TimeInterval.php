<?php
declare(strict_types=1);

namespace App\Lib;

use PhpParser\Node\Stmt\TryCatch;

class TimeInterval
{

	public int $seconds;
	public int $minutes;
	public int $hours;
	public int $days;

	function __construct(
		int $seconds = 0,
		int $minutes = 0,
		int $hours = 0,
		int $days = 0
	) {
		$this->seconds = $seconds;
		$this->minutes = $minutes;
		$this->hours = $hours;
		$this->days = $days;
		$this->carry();
	}

	private function carry() : void
	{
		if ($this->seconds >= 60) {
			$this->minutes += intdiv($this->seconds, 60);
			$this->seconds = $this->seconds % 60;
		}
		if ($this->minutes >= 60) {
			$this->hours += intdiv($this->minutes, 60);
			$this->minutes = $this->minutes % 60;
		}
		if ($this->hours >= 24) {
			$this->days += intdiv($this->hours, 24);
			$this->hours = $this->hours % 24;
		}
	}

	public function total_seconds() : int
	{
		return (
			 $this->seconds                +
			($this->minutes * 60)          +
			($this->hours   * 60 * 60)     +
			($this->days    * 60 * 60 * 24)
		);
	}

	public function min_format() : string
	{		
		$fmt = '';
		
		if ($this->days > 0) $fmt .= __("giorno_i", [$this->days,$this->days]) . " ";
		if ($this->hours > 0) $fmt .= __("ora_e", [$this->hours,$this->hours]) ." ";
		if ($this->minutes > 0) $fmt .= __("minuto_i", [$this->minutes,$this->minutes])." ";
		if ($this->seconds > 0) $fmt .= __("secondo_i", [$this->seconds, $this->seconds])." ";
		return $this->format($fmt);
	}

	public function format(string $fmt) : string
	{
		$this->carry(); // just in case
		$date_fmt = 'P';
		if ($this->days > 0) $date_fmt .= "{$this->days}D";		
		if (($this->hours > 0) || ($this->minutes > 0) || ($this->seconds > 0)) {
			$date_fmt .= 'T';
		}
		if ($this->hours > 0) $date_fmt .= "{$this->hours}H";
		if ($this->minutes > 0) $date_fmt .= "{$this->minutes}M";
		if ($this->seconds > 0) $date_fmt .= "{$this->seconds}S";
		try {
			$interval = new \DateInterval($date_fmt);
			return $interval->format($fmt);
		} catch (\Exception $e) {
			return "---";
		}
	}
}
