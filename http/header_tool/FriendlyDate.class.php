<?
class FriendlyDate extends Date
{
	function __construct($date = NULL)
	{
		$this->o_time = $date;
		return parent::__construct($date);
	}
	function create()
	{
		if($this->isToday())
		{
			return date("h:i:s a", $this->o_time);
		}
		else
		{
			return date("m/d h:i a", $this->o_time);
		}
	}
}
?>