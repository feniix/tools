<?php
/**
 * contains class Date and other date functions
 *
 * @copyright Copyright (C) 2003-2005  Sebastian Mendel <info at sebastianmendel dot de>
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License  - LGPL
 *
 * @package phpDateTime
 * @author Sebastian Mendel <info at sebastianmendel dot de>
 * @version $Id: Date.class.php,v 1.33 2005/04/21 10:06:20 cybot_tm Exp $
 * @source $Source: /cvsroot/phpdatetime/phpDateTime/Date.class.php,v $
 */

/**
 * defines parameters
 */
define('DATE_EMPTY_IF_NULL', 0);

/**
 * Class Date
 * 
 * usage:
 * <code>
 * $row = mysql_fetch_assoc(mysql_query('SELECT * FROM `table` LIMIT 1'));
 * $date = new Date($row['date']);  // $row['date'] = MySQL-TimeStamp, Unix-Timestamp or DateTime
 * echo $date->get(); // returns: 2004-05-09
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
class Date
{
    /**
     * the day
     * @access protected
     * @var integer
     */
    var $day;

    /**
     * the month
     * @access protected
     * @var integer
     */
    var $month;
    
    /**
     * the year
     * @access protected
     * @var integer
     */
    var $year;

    /**
     * century-divider
     *
     * if the given date-string has a 2-digit-year, then all years >= $century_divider are before 2000
     * and all years < $century_divider after 2000,
     * with a default of 70 a 2-digit-year can be from 1970 to 2069
     *
     * @access protected
     * @var integer
     **/
    var $century_divider    = 70;
    
    /**
     * constructor
     * usage:
     * <code>
     * $date = new Date();
     * echo $date->get(); // returns: 2004-05-09 (current Date)
     * 
     * $date = new Date(0);
     * echo $date->get(); // returns: 0000-00-00
     * 
     * $date = new Date('24.12.1987');
     * echo $date->get(); // returns: 1987-12-24
     * </code>
     *
     * @access protected
     * @uses Date::set() as return value
     * @param string|integer|object $date
     * @return Date::set()
     */
    function __construct( $date = NULL )
    {
        return $this->set( $date );
    }
    
    /**
     * returns true if date is 0 (0000-00-00), otherwise false
     * usage:
     * <code>
     * if ( $date->isNull() )
     * {
     *     echo 'unknown/not set';
     * }
     * else
     * {
     *     echo $date;
     * }
     * </code>
     *
     * @access public
     * @uses Date::getDay() to compare against 0
     * @uses Date::getMonth() to compare against 0
     * @uses Date::getYear() to compare against 0
     * @return boolean true or false
     * @todo make static
     */
    function isNull()
    {
        if ( $this->getDay() + $this->getMonth() + $this->getYear() === 0 )
        {
            return true;
        }
        return false;
    }
    
    /**
     * returns Date as string in ISO format (YYYY-MM-DD)
     *
     * @static 
     * @access public
     * @uses Date::getAsIso() as return value
     * @param mixed $date
     * @return string Date::getAsIso()
     */
    function get( $date = NULL )
    {
        if ( NULL === $date )
        {
            return $this->getAsIso( $date );
        }
        
        return Date::getAsIso( $date );
    }

    /**
     * day handler, returns day
     * 
     * @access public
     * @uses Date::$day as return value
     * @return Date::$day
     * @todo make static
     */
    function getDay()
    {
        return $this->day;
    }

    /**
     * month handler, returns month
     * 
     * @access public
     * @uses Date::$month as return value
     * @return Date::$month
     * @todo make static
     */
    function getMonth()
    {
        return $this->month;
    }

    /**
     * year handler, returns year
     * 
     * @access public
     * @uses Date::$year as return value
     * @return Date::$year
     * @todo make static
     */
    function getYear()
    {
        return $this->year;
    }
    
    /**
     * century_divider handler, returns century-divider
     * 
     * @access public
     * @uses Date::$century_divider as return value
     * @return Date::$century_divider
     */
    function getCenturyDivider()
    {
        return $this->century_divider;
    }
    
    /**
     * returns number of day in week
     * 0 = Sunday, 1 = Monday, ..., 6 = Saturday
     *
     * @access public
     * @uses Date::getAsStr() as return value
     * @return Date::getAsStr()
     * @todo make static
     */
    function getWeekDay()
    {
        return (int) $this->getAsStr('w');
    }

    /**
     * returns ISO8601 weeknumber
     *
     * <code>
     * echo Date::getWeek( '2003-12-30' ); // prints 1
     * echo Date::getWeek( '2004-06-30' ); // prints 27
     * echo Date::getWeek( '2005-01-02' ); // prints 52
     * </code>
     * 
     * @static
     * @access public
     * 
     * @uses getAsStr()
     * 
     * @param mixed $date
     * 
     * @return integer weeknumber
     */
    function getWeek( $date = NULL )
    {
        if ( NULL === $date )
        {
            return (int) $this->getAsStr('W');
        }
        
        $date =& new Date( $date );
        return $date->getWeek( NULL );
    }
    
    /**
     * returns ISO8601 Year of the Week
     * 
     * <code>
     * echo Date::getWeekYear( '2003-12-30' ); // prints 2004
     * echo Date::getWeekYear( '2005-01-02' ); // prints 2004
     * </code>
     * 
     * @since 2004-06-30
     * 
     * @static
     * @access public
     * 
     * @param mixed date
     * 
     * @return integer year
     */
    function getWeekYear()
    {
        if ( NULL === $date )
        {
            if ( $this->getMonth() === 12 && $this->getWeek() === 1 )
            {
                return $this->getYear() + 1;
            }
            elseif ( $this->getMonth() === 1 && $this->getWeek() > 50 )
            {
                return $this->getYear() - 1;
            }
            else
            {
                return $this->getYear();
            }
        }
        
        $date =& new Date( $date );
        return $date->getWeekYear( NULL );
    }
    
    /**
     * returns year-week (YYYYWW)
     * <code>
     * echo Date::getYearWeek( '2004-06-29' ); // prints 200427
     * </code>
     *
     * @version 2004
     * @version 2004-06-30
     * 
     * @uses sprintf()
     * @uses Date::getYear()
     * @uses Date::getWeek()
     * 
     * @static
     * @access public
     * 
     * @param mixed $date
     * 
     * @return string yearweek
     */
    function getYearWeek( $date = NULL )
    {
        if ( NULL === $date )
        {
            return sprintf('%04d%02d', $this->getYear(), $this->getWeek());
        }
        
        $date =& new Date( $date );
        return $date->getYearWeek( NULL );
    }
    
    /**
     * returns ISO8601 Week Date reduced extended format
     * if $reduced true in reduced format, without day
     * if $extended true in extended format, '-' between parts
     * 
     * <code>
     * echo Date::getWeekDate( '2003-12-30' ); // prints 2004-W01
     * echo Date::getWeekDate( '2004-06-29' ); // prints 2004-W27
     * echo Date::getWeekDate( '2004-06-29', false, false ); // prints 2004W272
     * </code>
     * 
     * @since 2004-06-29
     * @version 2004-06-30
     * 
     * @uses sprintf()
     * @uses Date::getWeekYear()
     * @uses Date::getWeek()
     * 
     * @static
     * @access public
     * 
     * @param mixed $date
     * @param boolean $reduced
     * @param boolean $extended
     * 
     * @return string Week Date
     */
    function getWeekDate( $date = NULL, $reduced = true, $extended = true )
    {
        if ( NULL === $date )
        {
            if ( $extended )
            {
                $extended = '-';
            }
            else
            {
                $extended = '';
            }
            
            $week_date = sprintf('%04d%sW%02d', $this->getWeekYear(), $extended, $this->getWeek());
            
            if ( ! $reduced )
            {
                $week_date .= $extended . $this->getWeekDay();
            }
            
            return $week_date;
        }
        
        $date =& new Date( $date );
        return $date->getWeekDate( NULL, $reduced, $extended );
    }

    /**
     * returns number of days in current month
     *
     * @access public
     * @uses getAsStr()
     * @return integer days_in_month
     * @todo make static
     */
    function getDaysInMonth()
    {
        return (int) $this->getAsStr('t');
    }
    
    /**
     * returns Date formated according to native php date()-function
     *
     * @access public
     * @uses Date::getAsTs()
     * @uses date()
     * @param string $format
     * @return string Formated Date
     * @todo make static
     */
    function getAsStr( $format )
    {
        return date($format, $this->getAsTs());
    }

    /**
     * returns abbreviated month name according to the current locale
     *
     * @deprecated
     *
     * @access public
     * @return string abbreviated monthname
     * @since v1.23
     * @uses Date::getAsLcStr()
     * @todo make static
     */
    function getLcMonthAbr()
    {
        return $this->getMonthNameAbbr();
    }
    
    /**
     * returns abbreviated month name according to the current locale
     *
     * @access public
     * @return string abbreviated monthname
     * @since v1.27
     * @uses Date::getAsLcStr()
     * @todo make static
     */
    function getMonthNameAbbr()
    {
        return $this->getAsLcStr('%b');
    }
    
    /**
     * returns weekday name according to the current locale
     *
     * @access public
     * @return string weekday_name
     * @since v1.27
     * @uses Date::getAsLcStr()
     * @todo make static
     */
    function getDayName()
    {
        return $this->getAsLcStr('%A');
    }
    
    /**
     * returns abbreviated weekday name according to the current locale
     *
     * @since 2004-06-30
     * @todo make static
     * 
     * @uses Date::getAsLcStr()
     * @access public
     * 
     * @return string abbreviated weekday_name
     */
    function getDayNameAbbr()
    {
        return $this->getAsLcStr('%a');
    }
    
    /**
     * Returns a string formatted according to the given format string
     *
     * @access public
     * @since v1.23
     * @param string $format
     * @see strftime()
     * @uses strftime()
     * @return string date
     * @todo make static
     */
    function getAsLcStr( $format )
    {
        return strftime( $format, $this->getAsTs() );
    }

    /**
     * returns Date as string in ISO format (YYYY-MM-DD)
     *
     * @access public
     * @static
     * @param mixed $date
     * @return string date
     * @since v1.0
     * @uses sprintf()
     * @uses Date::getYear()
     * @uses Date::getMonth()
     * @uses Date::getDay()
     * @todo make static
     */
    function getAsIso( $date = NULL )
    {
        if ( NULL === $date )
        {
            if ( isset( $this ) )
            {
                return sprintf("%04d-%02d-%02d", $this->getYear(), $this->getMonth(), $this->getDay());
            }
            
            $date = time();
        }
        
        $date = new Date( $date );
        return $date->getAsIso();
    }
    
    /**
     * returns Date as string in DIN format (DD.MM.YYYY)
     * if display_null = 1/true and IsNull() it returns 00.00.0000 otherwise an empty string
     *
     * @access public
     * @static
     * @param mixed $date
     * @return string date
     * @since v1.0
     * @uses sprintf()
     * @uses Date::getYear()
     * @uses Date::getMonth()
     * @uses Date::getDay()
     */
    function getAsDin( $date = NULL )
    {
        if ( NULL === $date )
        {
            if ( isset( $this ) )
            {
                return sprintf("%02d.%02d.%04d", $this->getDay(), $this->getMonth(), $this->getYear());
            }
            
            $date = time();
        }
        
        $date = new Date( $date );
        return $date->getAsDin();
    }
    
    /**
     * returns Date as string in US format (mm/dd/yyyy)
     *
     * @access public
     * @static
     * @param mixed $date
     * @return string date
     * @since v1.0
     * @uses sprintf()
     * @uses Date::getYear()
     * @uses Date::getMonth()
     * @uses Date::getDay()
     */
    function getAsAmi( $date = NULL )
    {
        if ( NULL === $date )
        {
            if ( isset( $this ) )
            {
                return sprintf("%02d/%02d/%04d", $this->getMonth(), $this->getDay(), $this->getYear());
            }
            
            $date = time();
        }
        
        $date = new Date( $date );
        return $date->getAsAmi();
    }
    
    /**
     * returns Date as timestamp
     *
     * @access public
     * @static
     * @param mixed $date
     * @return int unix-timestamp
     * @since v1.5
     *
     * @uses mktime()
     * @uses Date::getYear()
     * @uses Date::getMonth()
     * @uses Date::getDay()
     */
    function getAsTs( $date = NULL )
    {
        if ( NULL === $date )
        {
            if ( isset( $this ) )
            {
                return mktime(0, 0, 0, $this->getMonth(), $this->getDay(), $this->getYear());
            }
            
            $date = time();
        }
        
        $date = new Date( $date );
        return $date->getAsTs();
    }
    
    /**
     * Sets date from given date-string, date-object or unix-timestamp
     * return true on success, otherwise false
     *
     * @access public
     * @param mixed $date
     * @return string new date
     * @since v1.0
     * @version 2005-01-26
     *
     * @uses preg_match()
     * @uses is_numeric()
     * @uses time()
     * @uses trigger_error()
     * @uses E_USER_WARNING
     * @uses Date::setFromTs()
     * @uses Date::setFromDin()
     * @uses Date::setFromIso()
     * @uses Date::setFromAmi()
     */
    function set( $date = NULL )
    {
        if ( NULL === $date )
        {
            return $this->setFromTs( time() );
        }
        
        if ( is_numeric( $date ) )
        {
            return $this->setFromTs( $date );
        }
        
        if ( is_object( $date ) && get_class( $date ) == 'date' )
        {
            return $this->setFromIso( $date->getAsIso() );
        }
        
        if ( preg_match( '|\.|', $date ) )
        {
            // date in form d.m.y
            return $this->setFromDin( $date );
        }
        
        if ( preg_match( '|\/|', $date ) )
        {
            // date is in form m/d/y
            return $this->setFromAmi( $date );
        }
        
        if ( preg_match( '|\-|', $date ) )
        {
            // date is in form YYYY-MM-DD
            return $this->setFromIso( $date );
        }
        
        if ( empty( $date ) )
        {
            // date is '', so we use 0000-00-00
            return $this->setFromIso( '0000-00-00' );
        }
        
        trigger_error( 'unknown date-format: ' . var_export( $date, true ) . '(' . $_SERVER['REQUEST_URI'] . ')', E_USER_WARNING );
        return $this->setFromTs( time() );
    }
    
    /**
     * @access public
     * @param integer $day of month 0 to 31
     * @since v1.0
     *
     * @uses Date::$day
     */
    function setDay( $day )
    {
        $this->day = (int) $day;
        return true;
    }

    /**
     * @access public
     * @param integer $month 0 to 12
     * @since v1.0
     *
     * @uses Date::$month
     */
    function setMonth( $month )
    {
        $this->month = (int) $month;
        return true;
    }

    /**
     * @access public
     * @param integer $year (with century)
     * @since v1.0
     * @change v1.25 - added support for 2-digit-years
     *
     * @uses strlen()
     * @uses Date::getCenturyDivider()
     * @uses Date::$year
     */
    function setYear( $year )
    {
        if ( strlen( $year ) == 2 )
        {
            if ( $year >= $this->getCenturyDivider() )
            {
                $year += 1900;
            }
            else
            {
                $year += 2000;
            }
        }
        $this->year = (int) $year;
        return true;
    }
    
    /**
     * @access public
     * @param integer $century_divider
     * @since v1.25
     *
     * @uses Date::$century_divider
     */
    function setCenturyDivider( $century_divider )
    {
        if ( (int) $century_divider < 0 || (int) $century_divider > 99 )
        {
            // not in range
            return false;
        }
        
        $this->century_divider = (int) $century_divider;
        return true;
    }
    
    /**
     * Sets date from timestamp
     *
     * @access public
     * @param integer $timestamp
     * @since v1.3
     * @version 2004-06-30
     *
     * @uses date()
     * @uses Date::setYear()
     * @uses Date::setMonth()
     * @uses Date::setDay()
     */
    function setFromTs( $timestamp )
    {
	    // value must be some sort of Timestamp UNIX or MySQL < 4.1
	    // MySQL-Timestamp Values
	    $YY   = '([0-9]{2,4})';                    // =   00 -   9999
	    $MM   = '(0[0-9]{1}|1[0-2]{1})';           // =   00 -   12
	    $DD   = '([0-2]{1}[0-9]{1}|30|31)';        // =   00 -   31
	    $HH   = '([0-1]{1}[0-9]{1}|2[0-3]{1})';    // =   00 -   23
	    $SS   = '([0-5]{1}[0-9]{1})';              // =   00 -   59

        // MySQL-TIMESTAMP(14) 	YY(YY)MMDDHHMMSS
        // MySQL-TIMESTAMP(12) 	YYMMDDHHMMSS
        // MySQL-TIMESTAMP(10) 	YYMMDDHHMM
        // MySQL-TIMESTAMP(8) 	YYYYMMDD
        // MySQL-TIMESTAMP(6) 	YYMMDD
        // MySQL-TIMESTAMP(4) 	YYMM
        // MySQL-TIMESTAMP(2) 	YY
	    if ( preg_match('°^' . $YY . '(' . $MM . '(' . $DD . '(' . $HH . $SS . '(' . $SS . ')?)?)?)?$°', $timestamp, $date_parts ) )
	    {
            $this->setDay($date_parts[3]);
            $this->setMonth($date_parts[2]);
            $this->setYear($date_parts[1]);
	    }
        // a UNIX-TimeStamp ... ?
        elseif ( $timestamp > 0 )
        {
            $this->setFromUnixTimestamp( $timestamp );
        }
    }
    
    /**
     * sets date from given unix timestamp
     * 
     * @access public
     * @param int $unix_timestamp
     * @since 2004-06-30
     *
     * @uses date()
     * @uses Date::setYear()
     * @uses Date::setMonth()
     * @uses Date::setDay()
     */
    function setFromUnixTimestamp( $unix_timestamp )
    {
        $this->setDay( date('d', $unix_timestamp) );
        $this->setMonth( date('m', $unix_timestamp) );
        $this->setYear( date('Y', $unix_timestamp) );
    }
    
    /**
     * Sets date from DIN format (DD.MM.YYYY or D.M.YY)
     *
     * @access public
     * @param string $datestring
     * @since v1.0
     *
     * @uses explode()
     * @uses Date::setYear()
     * @uses Date::setMonth()
     * @uses Date::setDay()
     */
    function setFromDin( $datestring )
    {
        $datestring = trim( $datestring );
        
        // cut time
        $date = explode(' ', $datestring);
        
        // split date parts  dd.mm.yy
        $date = explode('.', $date[0]);

        $this->setDay($date[0]);
        $this->setMonth($date[1]);
        $this->setYear($date[2]);
    }
    
    /**
     * Sets date from US format: month/day/year
     * / can be any non-numeric character
     * m/d/y or m-d-y or m.d.y or m d y
     *
     * @access public
     * @param string $datestring
     * @since v1.0
     * @version 2004-08-03
     *
     * @uses explode()
     * @uses Date::setYear()
     * @uses Date::setMonth()
     * @uses Date::setDay()
     */
    function setFromAmi( $datestring )
    {
        //$date = explode('/', $datestring);
        // supports any non-numeric character between date parts
        $date = preg_split( '/[^0-9]+/', $datestring );
        /* removed - now in SetYear()
        if ( strlen($date[2]) === 2 )
        {
            $date[2] = ( ((int) strftime("%C")) * 100) + (int) $date[2];
        }
        */
        $this->setDay( $date[1] );
        $this->setMonth( $date[0] );
        $this->setYear( $date[2] );
        
        return $this->get();
    }
    
    /**
     * Sets date from ISO format (YYYY-MM-DD)
     *
     * @access public
     * @param string $datestring
     * @since v1.0
     *
     * @uses explode()
     * @uses Date::setYear()
     * @uses Date::setMonth()
     * @uses Date::setDay()
     */
    function setFromIso( $datestring )
    {
        $date = explode('-', $datestring);
        /* removed - now in SetYear()
        if ( strlen($date[2]) === 2 )
        {
            $date[2] = ( ((int) strftime("%C")) * 100) + (int) $date[2];
        }
        */
        $this->setDay($date[2]);
        $this->setMonth($date[1]);
        $this->setYear($date[0]);
    }
    
    /**
     * Sets date to first day of week
     *
     * @access public
     * @param integer $start_of_week 0 = Sunnday, 1 = Monday
     * @since v1.5
     *
     * @uses date()
     * @uses Date::getAsTs()
     * @uses Date::addDay()
     */
    function setToStartOfWeek( $start_of_week = 1 )
    {
        // get weekday-number
        $weekday_number = date('w', $this->getAsTs()) - $start_of_week;
        if ( $weekday_number === -1 )
        {
            // weekday-number is negative, ($start_of_week was 1 and day is Sunday!)
            $weekday_number = 6;
        }
        
        return $this->AddDay(-1 * $weekday_number);
    }
    
    /**
     * returns date for first day of week
     *
     * @access public
     * @param mixed $date
     * @param integer $start_of_week 0 = Sunday, 1 = Monday
     * @since v1.0
     * 
     * @static if called with first arguments not NULL
     *
     * @uses date()
     * @uses Date::getAsTs()
     * @uses Date::addDay()
     */
    function getStartOfWeek( $date = NULL, $start_of_week = 1 )
    {
        if ( NULL === $date )
        {
            $weekday_number = date('w', $this->getAsTs()) - $start_of_week;
            if ( $weekday_number < 0 )
            {
                // weekday-number is negative, ($start_of_week was 1 and day is Sunday!)
                $weekday_number += 7;
            }
            
            return $this->AddDay( -1 * $weekday_number, $this );
        }
        
        $date = new Date( $date );
        return $date->getStartOfWeek( NULL, $start_of_week );
    }
    
    
    /**
     * alias for Date::getLastWeek()
     *
     * @param mixed $date
     * @param integer $start_of_week 0 = Sunday, 1 = Monday
     * @uses Date::getLastWeek()
     */
    function getPrevWeek( $date = NULL, $start_of_week = 1 )
    {
        return Date::getLastWeek( $date, $start_of_week );
    }
    
    /**
     * returns date for last day of week
     *
     * @access public
     * @param mixed $date
     * @param integer $start_of_week 0 = Sunday, 1 = Monday
     * @since 2004-06-28
     * @version 2004-06-28
     * 
     * @static if called with first arguments not NULL
     *
     * @uses date()
     * @uses Date::getAsTs()
     * @uses Date::addDay()
     */
    function getEndOfWeek( $date = NULL, $start_of_week = 1 )
    {
        if ( NULL === $date )
        {
            $weekday_number = date('w', $this->getAsTs()) - $start_of_week;
            if ( $weekday_number < 0 )
            {
                // weekday-number is negative, ($start_of_week was 1 and day is Sunday!)
                $weekday_number += 7;
            }
            
            $weekday_number -= 6;
            
            return $this->AddDay( -1 * $weekday_number, $this );
        }
        
        $date = new Date( $date );
        return $date->getEndOfWeek( NULL, $start_of_week );
    }
    
    /**
     * returns date for first day of next week
     *
     * @access public
     * @param mixed $date
     * @param integer $start_of_week 0 = Sunday, 1 = Monday
     * @since 2004-06-28
     * @version 2005-01-25
     * 
     * @static if called with first argument not NULL
     *
     * @uses Date::addDay()
     * @uses Date::getStartOfWeek()
     */
    function getNextWeek( $date = NULL, $start_of_week = 1 )
    {
        if ( NULL === $date )
        {
            $date = $this;
        }
        
        return Date::getStartOfWeek( Date::addDay( 7, $date ), $start_of_week );
    }
    
    /**
     * returns date for first day of last week
     *
     * @access public
     * @param mixed $date
     * @param integer $start_of_week 0 = Sunday, 1 = Monday
     * @since 2005-01-25
     * @version 2005-01-25
     * 
     * @static if called with first argument not NULL
     *
     * @uses Date::addDay()
     * @uses Date::getStartOfWeek()
     */
    function getLastWeek( $date = NULL, $start_of_week = 1 )
    {
        if ( NULL === $date )
        {
            $date = $this;
        }
        
        return Date::getStartOfWeek( Date::addDay( -7, $date ), $start_of_week );
    }
    
    /**
     * adds given or 1 days to date
     *
     * @access public
     * @param integer $days
     * @param mixed $date
     * @return bool success
     * @since v1.3
     * @version 2004-06-28
     * 
     * @static if called with two arguments
     *
     * @uses mktime()
     * @uses Date::setFromUnixTimestamp()
     * @uses Date::getYear()
     * @uses Date::getMonth()
     * @uses Date::getDay()
     * 
     * @return string new date
     */
    function addDay( $days = 1, $date = NULL )
    {
        if ( NULL === $date )
        {
            $ts = mktime(0, 0, 0, $this->getMonth(), $this->getDay() + $days, $this->getYear());
            $this->setFromUnixTimestamp( $ts );
            return $this->get();
        }
        
        $date = new Date( $date );
        return $date->addDay( $days, NULL );
    }
    
    /**
     * returns date for next day
     *
     * @access public
     * @since 2004-06-28
     * @version 2004-06-28
     *
     * @static
     * @param mixed $date
     * 
     * @uses Date::addDay()
     */
    function getNextDay( $date = NULL )
    {
        if ( NULL === $date )
        {
            return $this->addDay( 1, $this );
        }
        return Date::addDay( 1, $date );
    }
    
    /**
     * returns date for prev day
     *
     * @access public
     * @since 2004-06-28
     * @version 2004-06-28
     *
     * @static
     * @param mixed $date
     * 
     * @uses Date::addDay()
     */
    function getPrevDay( $date = NULL )
    {
        if ( NULL === $date )
        {
            return $this->addDay( -1, $this );
        }
        return Date::addDay( -1, $date );
    }
    
    /**
     * checks if date is today
     *
     * @access public
     * @return bool true or false
     * @since v1.15
     *
     * @uses date()
     * @uses Date::getAsIso()
     * @todo make static
     */
    function isToday()
    {
        if ( $this->getAsIso() === date('Y-m-d') )
        {
            return true;
        }
        return false;
    }
    
    /**
     * checks if date is yesterday
     *
     * @access public
     * @return bool true or false
     * @since v1.26
     *
     * @uses date()
     * @uses time()
     * @uses Date::getAsIso()
     * @todo make static
     */
    function isYesterday()
    {
        if ( $this->getAsIso() === date('Y-m-d', time() - 60 * 60 * 24) )
        {
            return true;
        }
        return false;
    }
    
    /**
     * checks if date is tomorrow
     *
     * @access public
     * @return bool true or false
     * @since v1.26
     *
     * @uses date()
     * @uses time()
     * @uses Date::getAsIso()
     * @todo make static
     */
    function isTomorrow()
    {
        if ( $this->getAsIso() === date('Y-m-d', time() + 60 * 60 * 24) )
        {
            return true;
        }
        return false;
    }
    
    /**
     * checks if date is in current month
     *
     * @access public
     * @return bool true or false
     * @since v1.23
     *
     * @uses date()
     * @uses Date::getMonth()
     * @uses Date::getYear()
     * @todo make static
     */
    function isCurrentMonth()
    {
        if ( $this->getMonth() == date('m') &&  $this->getYear() == date('Y') )
        {
            return true;
        }
        return false;
    }
    
    /**
     * checks if date is in current month
     *
     * @access public
     * @return bool true or false
     * @since v1.23
     *
     * @uses date()
     * @uses Date::getWeek()
     * @uses Date::getYear()
     * @todo make static
     */
    function isCurrentWeek()
    {
        if ( $this->getWeek() == date('W') &&  $this->getYear() == date('Y') )
        {
            return true;
        }
        return false;
    }
    
    /**
     * return true if date is Friday, otherwise false
     *
     * @access public
     * @return bool true or false
     * @since v1.8
     *
     * @uses date()
     * @uses Date::getAsTs()
     * @todo make static
     */
    function isFriday()
    {
        if ( date('w', $this->getAsTs()) === '5' )
        {
            return true;
        }
        
        return false;
    }

    /**
     * return true if date is Sunday, otherwise false
     *
     * @access public
     * @return bool true or false
     * @since v1.9
     *
     * @uses date()
     * @uses Date::getAsTs()
     * @todo make static
     */
    function isSunday()
    {
        if ( date('w', $this->getAsTs()) === '0' )
        {
            return true;
        }
        
        return false;
    }

    /**
     * return true if date is Sunday, otherwise false
     *
     * @access public
     * @return bool true or false
     * @since v1.9
     *
     * @uses date()
     * @uses Date::getAsTs()
     * @todo make static
     */
    function isSaturday()
    {
        if ( date('w', $this->getAsTs()) === '6' )
        {
            return true;
        }
        
        return false;
    }

    /**
     * return true if date is first day in year, otherwise false
     *
     * @access public
     * @return bool true or false
     * @since v1.10
     *
     * @uses Date::getDay()
     * @uses Date::getMonth()
     * @todo make static
     */
    function isNewYear()
    {
        if ( $this->getDay() === 1 && $this->getMonth() === 1 )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * checks if date is less then given date
     * if no date is given current date is used
     *
     * @access public
     * @param mixed $date
     * @return bool true or false
     * @since v1.17
     *
     * @uses date()
     * @uses Date::get()
     * @uses Date
     * @todo make static
     */
    function isLt( $date = NULL )
    {
        if ( $date === NULL )
        {
            $date = date( 'Y-m-d' );
        }
        $date = new Date( $date );
        
        if ( $this->get() < $date->get() )
        {
            return true;
        }
        
        return false;
    }

    /**
     * checks if date is greater then or equal to given date
     * if no date is given current date is used
     *
     * @access public
     * @param mixed $date
     * @return bool true or false
     * @since v1.17
     *
     * @uses Date::isGt()
     * @uses Date::isEq()
     * @todo make static
     */
    function isGe( $date = NULL )
    {
        if ( $this->isGt( $date ) || $this->isEq( $date ) )
        {
            return true;
        }
        
        return false;
    }

    /**
     * checks if date is less then or equal to given date
     * if no date is given current date is used
     *
     * @access public
     * @param mixed $date
     * @return bool true or false
     * @since v1.17
     *
     * @uses Date::isLt()
     * @uses Date::isEq()
     * @todo make static
     */
    function isLe( $date = NULL )
    {
        if ( $this->isLt( $date ) || $this->isEq( $date ) )
        {
            return true;
        }
        
        return false;
    }

    /**
     * checks if date is equal to given date
     * if no date is given current date is used
     *
     * @access public
     * @param mixed $date
     * @return bool true or false
     * @since v1.17
     *
     * @uses date()
     * @uses Date::get()
     * @uses Date
     * @todo make static
     */
    function isEq( $date = NULL )
    {
        if ( $date === NULL )
        {
            $date = date( 'Y-m-d' );
        }
        $date = new Date( $date );
        
        if ( $this->get() === $date->get() )
        {
            return true;
        }
        
        return false;
    }

    /**
     * checks if date is greater then given date
     * if no date is given current date is used
     *
     * @access public
     * @param mixed $date
     * @return boolean true or false
     * @since v1.17
     *
     * @uses date()
     * @uses Date::get()
     * @uses Date
     * @todo make static
     */
    function isGt( $date = NULL )
    {
        if ( $date === NULL )
        {
            $date = date( 'Y-m-d' );
        }
        $date = new Date( $date );
        
        if ( $this->get() > $date->get() )
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * checks if date is between date1 and date2
     *
     * @since v1.18
     * @access public
     * @uses Date::isGt()
     * @uses Date::isLt()
     * @param mixed $date1
     * @param mixed $date2
     * @return boolean true if date is between date1 and date2 or false if not
     * @todo make static with third argument
     */
    function isBetween( $date1, $date2 )
    {
        if ( $this->isGt( $date1 ) && $this->isLt( $date2 ) )
        {
            return true;
        }
        elseif ( $this->isGt( $date2 ) && $this->isLt( $date1 ) )
        {
            return true;
        }
        
        return falsE;
    }
    
    /**
     * Returns an array with all month names for current locale
     * <code>
     * array( 1 => 'Januar', 2 => 'Februar', 3 => ... );
     * </code>
     *
     * @static
     * @access public
     * @uses strftime()
     * @uses mktime()
     * @return array month_names
     * @todo add optional paramter for locale, e.g. 'de-DE'
     */
    function getLocalMonthNames()
    {
        $month_names = array();
        
        for ( $month = 1; $month <= 12; $month++ )
        {
            $month_names[$month] = strftime('%B', mktime(1, 1, 1, $month, 1, 1));
        }
        
        return $month_names;
    }

    /**
     * returns next day Date object
     *
     * @access public
     * @uses Date as return value
     * @uses Date::addDay()
     * @return object Date nextday
     * @todo make static
     */
    function nextDay()
    {
        $nextday = $this;
        $nextday->addDay(1);
        return $nextday;
    }

    /**
     * returns next day Date object
     *
     * @access public
     * @uses Date as return value
     * @uses Date::addDay()
     * @return object Date previousday
     * @todo make static
     */
    function previousDay()
    {
        $previousday = $this;
        $previousday->addDay(-1);
        return $previousday;
    }
}

/**
 * returns true if given string is a valid date
 * otherwise false
 *
 * @param string $date
 * @return bool true if given string evaluates to a valid date or false if not
 *
 * @uses preg_match()
 *
 **/
function _is_date( $date )
{
    if ( preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/', $date) )
    {
        return true;
    }
    
    return false;
}

?>