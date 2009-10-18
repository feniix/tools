<?php
/**
 * Time class and other time functions
 *
 * @copyright Copyright (C) 2003, 2004  Sebastian Mendel <info at sebastianmendel dot de>
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License  - LGPL
 *
 * @package phpDateTime
 * @author Sebastian Mendel <info at sebastianmendel dot de>
 * @version $Id: Time.class.php,v 1.18 2005/03/02 09:42:09 cybot_tm Exp $
 * @source $Source: /cvsroot/phpdatetime/phpDateTime/Time.class.php,v $
 */

/**
 * defines parameters
 */
define('TIME_OMIT_SIGN',  1);
define('TIME_FORCE_SIGN', 2);
define('TIME_OMIT_NULL',  4);

/**
 * Class Time
 * 
 * example:
 * <code>
 * $row = mysql_fetch_assoc( mysql_query(SELECT `time` FROM `table`) );
 * $time = new Time($row['time']);
 * echo $time->get(); // prints: 12:23:12 or 125:30
 * </code>
 *
 * @copyright Copyright (C) 2003, 2004  Sebastian Mendel <info at sebastianmendel dot de>
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License  - LGPL
 *
 * @package phpDateTime
 * @author Sebastian Mendel <info@sebastianmendel.de>
 */
class Time
{
    /**
     * the hours
     * @var integer
     * @access protected
     */
    var $hours = 0;

    /**
     * the minutes
     * @var integer
     * @access protected
     */
    var $minutes = 0;

    /**
     * the seconds
     * @var integer
     * @access protected
     */
    var $seconds = 0;

    /**
     * the microseconds
     * @var integer
     * @access protected
     */
    var $fractals = 0;

    /**
     * the sign (-1, +1)
     * @var integer
     * @access protected
     */
    var $sign = 1;

    /**
     * Constructor
     * defines inital value for time
     * expects Time in form of seconds as int
     * or in form H:M[:S[.M]] as string
     * or null, if $time is is null then current time is taken
     * or an object Time
     * 
     * @access protected
     * @uses Time::set() to set time and returns this
     * @param string|int|object Time
     * @return Time::set()
     * @todo should default lead to 0 or NOW() ???
     */
    function Time( $time = null )
    {
        return $this->set( $time );
    }

    /**
     * returns time-string in form [H]HH:MM[:SS[:s]]
     *
     * @access public
     * @uses Time if called statically
     * @uses Time::getSign()
     * @uses Time::getHours()
     * @uses Time::getMinutes()
     * @static
     * @param integer|string|object Time
     * @return string time
     * @todo add non-null values for
     *      seconds and microseconds to return value
     */
    function get( $time = NULL )
    {
        if ( NULL === $time )
        {
            if ( $this->getSign() === -1 )
            {
                $sign = '-';
            }
            else
            {
                $sign = '';
            }
            
            return $sign . sprintf("%02d:%02d:%02d", $this->getHours(), $this->getMinutes(), $this->getSeconds() );
        }
        
        $time = new Time( $time );
        return $time->get();
    }

    /**
     * handler for hours, returns hours
     *
     * @access public
     * @uses Time::$hours
     * @static
     * @param integer|string|object Time
     * @return Time::$hours
     */
    function getHours( $time = null )
    {
        if ( NULL === $time )
        {
            return $this->hours;
        }
        
        $time = new Time( $time );
        return $time->getHours();
    }

    /**
     * handler for minutes, returns minutes
     *
     * @access public
     * @uses Time::$minutes
     * @static
     * @param integer|string|object Time
     * @return Time::$minutes
     */
    function getMinutes( $time = null )
    {
        if ( NULL === $time )
        {
            return $this->minutes;
        }
        
        $time = new Time( $time );
        return $time->getMinutes();
    }

    /**
     * handler for seconds, returns seconds of time
     *
     * @access public
     * @uses Time::$seconds
     * @static
     * @param integer|string|object Time
     * @return Time::$seconds
     */
    function getSeconds( $time = null )
    {
        if ( NULL === $time )
        {
            return $this->seconds;
        }
        
        $time = new Time( $time );
        return $time->getSeconds();
    }

    /**
     * handler for sign, returns sign as -1 or +1
     *
     * @access public
     * @uses Time::$sign
     * @static
     * @param integer|string|object Time
     * @return Time::$sign
     */
    function getSign( $time = null )
    {
        if ( NULL === $time )
        {
            return $this->sign;
        }
        
        $time = new Time( $time );
        return $time->getSign();
    }

    /**
     * returns true if time is negative
     *
     * @access public
     * @uses Time::getSign()
     * @static
     * @param integer|string|object Time
     * @return boolean true or false
     */
    function isNeg( $time = null )
    {
        if ( NULL === $time )
        {
            if ( $this->getSign() < 0 )
            {
                return true;
            }
            return false;
        }
        
        $time = new Time( $time );
        if ( $time->getSign() < 0 )
        {
            return true;
        }
        return false;
    }
    
    /**
     * alias for Time:isNeg()
     *
     * @access public
     * @uses Time::isNeg()
     * @static
     * @param integer|string|object Time
     * @return Time::isNeg()
     */
    function isNegative( $time = null )
    {
        if ( isset($this) )
        {
            return $this->isNeg( $time );
        }
        else
        {
            return Time::isNeg( $time );
        }
    }

    /**
     * checks if current time is 0
     * returns true if time is 0, otherwise false
     *
     * @access public
     * @uses Time::getAsSeconds()
     * @static
     * @param integer|string|object Time
     * @return boolean true or false
     *
     */
    function isNull( $time = null )
    {
        if ( NULL === $time )
        {
            if ( $this->getAsSeconds() === 0 )
            {
                return true;
            }
    
            return false;
        }
        
        $time = new Time( $time );
        if ( $time->getAsSeconds() === 0 )
        {
            return true;
        }

        return false;
    }

    /**
     * returns time in seconds
     *
     * @access public
     * @uses Time if called statically
     * @uses Time::getSign()
     * @uses Time::getHours()
     * @uses Time::getMinutes()
     * @uses Time::getSeconds()
     * @param integer|string|object Time
     * @return integer seconds
     * @static
     * @todo add support for microseconds
     */
    function getAsSeconds( $time = NULL )
    {
        if ( NULL === $time )
        {
            return $this->getSign() * (($this->getHours() * 60 * 60) + ($this->getMinutes() * 60) + ($this->getSeconds()));
        }
        
        $time = new Time( $time );
        return $time->getAsSeconds();
    }

    /**
     * handler for sign, sets sign
     *
     * @access public
     * @uses Time::$sign
     * @param integer sign
     */
    function setSign( $sign )
    {
        $this->sign = (int) $sign;
    }

    /**
     * handler for hours, sets hours
     *
     * @access public
     * @uses Time::$hours
     * @param integer hours
     */
    function setHours( $hours )
    {
        $this->hours = (int) $hours;
    }

    /**
     * handler for minutes, sets minutes
     *
     * @access public
     * @uses Time::$minutes
     * @param integer minutes
     */
    function setMinutes( $minutes )
    {
        $this->minutes = (int) $minutes;
    }

    /**
     * hanler for seconds, sets seconds
     *
     * @access public
     * @uses Time::$seconds
     * @param integer seconds
     */
    function setSeconds( $seconds )
    {
        $this->seconds = (int) $seconds;
    }

    /**
     * sets time to given value
     * 
     * @access public
     * @uses Time::setTimeFromString()
     * @uses Time::setTimeFromSeconds()
     * @param string|int|object Time
     * @return boolean|string false or time
     */
    function set( $time = null )
    {
        if ( is_object($time) && get_class($time) == 'time' )
        {
            return $this->setTimeFromSeconds($time->getAsSeconds());
        }
        
        if ( is_numeric($time) )
        {
            return $this->setTimeFromSeconds($time);
        }
        
        return $this->setTimeFromString($time);
    }

    /**
     * sets time from given seconds,
     * returns time as string
     *
     * @access public
     * @uses Time::get() as return value
     * @uses Time::setSign()
     * @uses Time::setHours()
     * @uses Time::setMinutes()
     * @uses Time::setSeconds()
     * @param integer|float seconds
     * @return Time::get()
     * @todo recognize micro-seconds in float value
     */
    function setTimeFromSeconds( $seconds = 0.0 )
    {
        $seconds = (float) $seconds;

        if ( $seconds < 0 )
        {
            $seconds = $seconds * -1;
            $this->setSign(-1);
        }
        else
        {
            $this->setSign(1);
        }

        $this->setHours(floor($seconds / 60 / 60));
        $seconds = $seconds % ( 60 * 60 );
        $this->setMinutes(floor($seconds / 60 ));
        $seconds = $seconds % 60;
        $this->setSeconds($seconds);

        return  $this->get();
    }

    /**
     * sets time from string in form [H]HH:MM[:SS[.s]]
     * returns new time
     *
     * @access public
     * @uses Time::get() as return value
     * @uses Time::setSign()
     * @uses Time::setHours()
     * @uses Time::setMinutes()
     * @uses Time::setSeconds()
     * @param string time
     * @return Time::get()
     * @todo implement support for micro-seconds
     */
    function setTimeFromString( $time )
    {
        preg_match("/(\-)?([0-9]*):([0-5]{1}[0-9]{1})(:([0-5]{1}[0-9]{1}(.[0-9]*)?))?$/", $time, $time_split);

        if ( isset($time_split[1]) && $time_split[1] == '-' )
        {
            $this->setSign(-1);
        }
        else
        {
            $this->setSign(1);
        }

        if ( isset($time_split[2]) )
        {
            $this->setHours($time_split[2]);
        }

        if ( isset($time_split[3]) )
        {
            $this->setMinutes($time_split[3]);
        }

        if ( isset($time_split[5]) )
        {
            $this->setSeconds($time_split[5]);
        }

        return $this->get();
    }

    /**
     * Adds time
     * returns new time
     *
     * @access public
     * @uses Time
     * @uses Time::getAsSeconds()
     * @uses Time::setTimeFromSeconds()
     * @uses Time::get() as return value
     * @param integer|string|object Time
     * @return string Time::get()
     */
    function add( $time )
    {
        $mytime = new Time( $time );
        $new_time = $this->getAsSeconds() + $mytime->getAsSeconds();
        $this->setTimeFromSeconds($new_time);
        return $this->get();
    }

    /**
     * subtracts time
     * returns new time
     * 
     * @access public
     * @uses Time
     * @uses Time::getAsSeconds()
     * @uses Time::setTimeFromSeconds()
     * @uses Time::get() as return value
     * @param integer|string|object Time
     * @return string Time::get()
     */
    function sub( $time )
    {
        $mytime = new Time($time);
        $new_time = $this->getAsSeconds() - $mytime->getAsSeconds();
        $this->setTimeFromSeconds($new_time);
        return $this->get();
    }

    /**
     * returns new time
     * 
     * @access public
     * @uses Time
     * @uses Time::getAsSeconds()
     * @uses Time::setTimeFromSeconds()
     * @uses Time::get() as return value
     * @param integer|string|object Time
     * @return string Time::get()
     */
    function mul( $multiplicator )
    {
        $new_time = $this->getAsSeconds() * $multiplicator;
        $this->setTimeFromSeconds($new_time);
        return $this->get();
    }

    /**
     * returns new time
     * 
     * @access public
     * @uses Time
     * @uses Time::getAsSeconds()
     * @uses Time::setTimeFromSeconds()
     * @uses Time::get() as return value
     * @param integer|string|object Time
     * @return string Time::get()
     */
    function div( $divider )
    {
        $new_time = $this->getAsSeconds() / $divider;
        $this->setTimeFromSeconds($new_time);
        return $this->get();
    }

    /**
     * returns new time
     * 
     * @access public
     * @uses Time
     * @uses Time::getAsSeconds()
     * @uses Time::setTimeFromSeconds()
     * @uses Time::get() as return value
     * @param integer|string|object Time
     * @return string Time::get()
     * @todo finish
     */
    function diff( $time )
    {
        $mytime = new Time($time);
        $time1 = $this->getAsSeconds();
        $time2 = $mytime->getAsSeconds();
        // @todo finish
        $this->setTimeFromSeconds($new_time);
        return $this->get();
    }
}

/**
 * is_time() returns true if given string is in correct Time-Format HHH:MM:SS
 *
 * @param string time
 * @return bool is_time
 *
 * @uses preg_match()
 * @version 1.0.1
 * @changed 1.0.1 - accepts now more then 24 hours in Time-String
 *
 */
function is_time($time)
{
    // accepts HHHH:MM:SS, e.g. 23:59:30 or 12:30 or 120:17
    if ( ! preg_match("/^(\-)?[0-9]{1,4}:[0-9]{1,2}(:[0-9]{1,2})?$/", $time) )
    {
        return false;
    }

    return true;
}

/**
 * returns Time1 with the value of Time2 subtracted
 *
 * @param string Time
 * @param string Time
 * @return string Time
 *
 * @uses strlen()
 * @uses substr()
 * @uses is_valid_time()
 * @uses Time::getAsSeconds()
 * @uses Time::get()
 *
 */
function time_sub($time1, $time2)
{
    if ( empty($time1) )
    {
        $time1 = '00:00';
    }

    if ( empty($time2) )
    {
        $time2 = '00:00';
    }

    if ( ! is_time($time1) || ! is_time($time2) )
    {
        return false;
    }

    $time = Time::get( Time::getAsSeconds($time1) - Time::getAsSeconds($time2) );

    // @todo change the check for seconds/returned time-format
    if ( strlen($time1) > 6 || strlen($time2) > 6 )
    {	// if given time was with seconds return with seconds also
        return $time;
    }
    else
    {
        // else discard seconds
        return substr($time, 0, -3);
    }
}

/**
 * returns sum of Time1 and Time2
 *
 * @param string Time
 * @param string Time
 * @return string Time
 *
 * @uses strlen()
 * @uses substr()
 * @uses is_valid_time()
 * @uses Time::getAsSeconds()
 * @uses Time::get()
 *
 */
function time_add($time1, $time2)
{
    if ( empty($time1) )
    {
        $time1 = '00:00';
    }

    if ( empty($time2) )
    {
        $time2 = '00:00';
    }

    if ( ! is_time($time1) || ! is_time($time2) )
    {
        return false;
    }

    $time = Time::get( Time::getAsSeconds($time1) + Time::getAsSeconds($time2) );

    // @todo change the check for seconds/returned time-format
    if ( strlen($time1) > 6 || strlen($time2) > 6 )
    {	// if given time was with seconds return with seconds also
        return $time;
    }
    else
    {
        // else discard seconds
        return substr($time, 0, -3);
    }
}
?>