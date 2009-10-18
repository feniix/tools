<?php
/**
 * contains class DateTime
 *
 * @copyright Copyright (C) 2003, 2004  Sebastian Mendel <info at sebastianmendel dot de>
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License  - LGPL
 *
 * @package phpDateTime
 * @author Sebastian Mendel <info@sebastianmendel.de>
 * @version $Id: DateTime.class.php,v 1.18 2005/03/08 16:06:40 cybot_tm Exp $
 * @source $Source: /cvsroot/phpdatetime/phpDateTime/DateTime.class.php,v $
 *
 * @uses time.class.php
 * @uses date.class.php
 */

/**
 * required class-files
 */
require_once 'Time.class.php';
require_once 'Date.class.php';

/**
 * Class DateTime
 *
 * @copyright Copyright (C) 2003, 2004  Sebastian Mendel <info at sebastianmendel dot de>
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License  - LGPL
 *
 * @package phpDateTime
 * @author Sebastian Mendel <info@sebastianmendel.de>
 * @uses Time
 * @uses Date
 */
class DateTime
{
    /**
     * @var object time, time Object of class Time
     * @access protected
     */
    var $time;

    /**
     * @var object date, date Object of class Date
     * @access protected
     */
    var $date;

    /**
     * DateTime
     *
     * @access protected
     *
     * @param string DateTime or Date in form [Date] [Time], e.g. YYYY-MM-DD HH:MM:SS
     * @param string Time in form [Time], e.g. HH:MM:SS
     *
     * @uses split()
     * @uses DateTime::setTime()
     * @uses DateTime::setDate()
     *
     * @todo we should add the functionality to determine if given (first) string is time, date or datetime
     *
     */
    function DateTime($datetime = null, $time = null)
    {
        return $this->set($datetime, $time);
    }

    /**
     * set
     *
     * @access public
     * @param string DateTime or Date in form [Date] [Time], e.g. YYYY-MM-DD HH:MM:SS
     * @param string Time in form [Time], e.g. HH:MM:SS
     *
     * @uses is_numeric()
     * @uses split()
     * @uses preg_match()
     * @uses DateTime::setTime()
     * @uses DateTime::setDate()
     * @uses DateTime::get() as return value
     * @return DateTime::get()
     *
     * @todo we should add the functionality to determine if given (first) string is time, date or datetime
     *
     */
    function set($datetime = null, $time = null)
    {
    	if ( null === $datetime )
    	{
            $this->setTime(0);
            $this->setDate(0);
            return true;
    	}
    	
    	if ( is_numeric($datetime) && $datetime > 0 )
    	{
    	    // wo do not support mysql-timestamp (YYYYMMDDHHIISS) any more
            $date = date('Y-m-d', $datetime);
            $_time = date('H:i:s', $datetime);
            
            if ( null === $time )
            {
                $time = $_time;
            }
            
            $this->setTime($time);
            $this->setDate($date);
            return true;
    	}

    	if ( is_a( $datetime, 'DateTime' ) )
    	{
            $this->setTime( $datetime->time );
            $this->setDate( $datetime->date );
            return true;
    	}
    	
    	// is it ISO-Format?
    	// we expect any sort of '[Date] [Time]'
        if ( null === $time )
        {
            $date_parts = split(' ', $datetime);
            $date = $date_parts[0];
            if ( isset($date_parts[1]) )
            {
                $time = $date_parts[1];
            }
            else
            {
                $time = 0;
            }
        }
        
        // @todo add check for UK, US, DIN

        $this->setTime($time);
        $this->setDate($datetime);
        
        return $this->get();
    }
    
    /**
     * Sets Time Object
     *
     * @access public
     * @param string|integer|object Time
     *
     * @uses DateTime::$time
     * @uses Time
     */
    function setTime( $time = NULL )
    {
        $this->time = new Time($time);
    }

    /**
     * Sets Date Object
     *
     * @access public
     * @param string|integer|object Date
     *
     * @uses DateTime::$date
     * @uses Date
     */
    function setDate( $date = NULL )
    {
        $this->date = new Date($date);
    }

    /**
     * returns DateTime in standard format (ISO)
     *
     * @static
     * @access public
     * @uses Date::get()
     * @uses Time::get()
     * @uses DateTime::$date
     * @uses DateTime::$time
     *
     * @return string concatenated Date::get() and Time::get()
     */
    function get( $datetime = NULL )
    {
        if ( NULL === $datetime )
        {
            return $this->date->get() . ' ' . $this->time->get();
        }
        
        $datetime = new DateTime( $datetime );
        return $datetime->get();
    }

    /**
     * returns DateTime in DIN format
     *
     * @access public
     * @uses Date::getAsDin()
     * @uses Time::get()
     * @uses DateTime::$date
     * @uses DateTime::$time
     *
     * @return string concatenated Date::getAsdin() and Time::get()
     */
    function getAsDin()
    {
        return $this->date->getAsDin() . ' ' . $this->time->get();
    }

    /**
     * returns UNIX-Timestamp (seconds since unix-epoche)
     *
     * @access public
     * @uses Date::getAsTs()
     * @uses Time::getAsSeconds()
     * @uses DateTime::$date
     * @uses DateTime::$time
     *
     * @return integer
     */
    function getAsTs()
    {
        return $this->date->getAsTs() + $this->time->getAsSeconds();
    }
}
?>