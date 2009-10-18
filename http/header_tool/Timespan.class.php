<?php
/**
 * contains class Timespan
 *
 * @copyright Copyright (C) 2003-2005  Sebastian Mendel <info at sebastianmendel dot de>
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License  - LGPL
 *
 * @package phpDateTime
 * @author Sebastian Mendel <info at sebastianmendel dot de>
 * @version $Id: Timespan.class.php,v 1.14 2005/03/08 16:20:01 cybot_tm Exp $
 * @source $Source: /cvsroot/phpdatetime/phpDateTime/Timespan.class.php,v $
 *
 * @uses DateTime.class.php
 */

/**
 * required class-files
 */
require_once 'DateTime.class.php';

/**
 * Class Timespan
 *
 * @copyright Copyright (C) 2003, 2004  Sebastian Mendel <info at sebastianmendel dot de>
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License  - LGPL
 *
 * @package phpDateTime
 * @author Sebastian Mendel <info@sebastianmendel.de>
 * @uses DateTime
 * @todo finish class
 */
class Timespan
{
    /**
     * start time
     * @access protected
     * @var object DateTime
     */
    var $start;

    /**
     * end time
     * @access protected
     * @var object DateTime
     */
    var $end;

    /** 
     * @var integer fullyears */
    var $fullremyears   = 0;

    /** 
     * @var integer full months */
    var $fullremmonths  = 0;

    /** 
     * @var integer fulldays */
    var $fullremdays    = 0;

    /** 
     * @var integer years */
    var $years  = 0;

    /** 
     * @var integer months */
    var $months = 0;

    /** 
     * @var integer days */
    var $days   = 0;
        
    /** 
     * @var integer fullyears */
    var $fullremhours   = 0;

    /** 
     * @var integer full months */
    var $fullremminutes  = 0;

    /** 
     * @var integer fulldays */
    var $fullremseconds    = 0;

    /** 
     * @var integer years */
    var $hours  = 0;

    /** 
     * @var integer months */
    var $minutes = 0;

    /** 
     * @var integer days */
    var $seconds   = 0;
    
    /**
     * @uses Timespan::set()
     * @access protected
     * @param string|integer|object_DateTime $start
     * @param string|integer|object_DateTime $end
     */
    function Timespan( $start = 0, $end = NULL )
    {
        $this->set( $start, $end );
    }

    /**
     * sets start and end-time
     *
     * @uses Timespan::setStart()
     * @uses Timespan::setEnd()
     * @uses Date::setToStartOfWeek()
     * @uses Date::setToEndOfWeek()
     * @param string|integer|object_DateTime $start
     * @param string|integer|object_DateTime $end
     * @todo finish
     */
    function set( $start = 0, $end = NULL )
    {
        $this->setStart( $start );
        
        if ( NULL === $end )
        {
            $this->setEnd( $start );
        }
        else
        {
            switch ( $end )
            {
            	case 'year': 
                    // add one year to end
            		break;
            	case 'month':
            		// add one month
            		break;
            	case 'week': 
                    // add one year to end
            		break;
            	case 'day': 
                    // add one year to end
            		break;
            	case 'calendarweek': 
                    // set start and end date to start and end of week for current date
                    $this->start->date->setToStartOfWeek();
                    $this->setEnd( $start );
                    $this->end->date->setToEndOfWeek();
            		break;
            	default:
                    $this->setEnd( $end );
            } // switch
        }
        
        //$this->updatePorperties();
    }
    
    /**
     * sets start-date
     *
     * @uses Timespan::start to set it
     * @uses DateTime
     * @uses DateTime::get()
     * @param string|integer|object_DateTime $datetime
     * @return string datetime
     */
    function setStart( $datetime )
    {
        $this->start = new DateTime( $datetime );
        $this->updatePorperties();
        return $this->start->get();
    }
    
    /**
     * stes end-date
     *
     * @uses Timespan::end to set it
     * @uses DateTime
     * @uses DateTime::get()
     * @param string|integer|object_DateTime $datetime
     * @return string datetime
     */
    function setEnd( $datetime )
    {
        $this->end = new DateTime( $datetime );
        $this->updatePorperties();
        return $this->end->get();
    }
    
    /**
     *
     * @uses Timespan::getDifference()
     * @return integer
     */
    function getYearsRemainder()
    {
        return $this->fullremyears;
    }

    /**
     *
     * @uses Timespan::getDifference()
     * @return integer
     */
    function getMonthsRemainder()
    {
        return $this->fullremmonths;
    }

    /**
     *
     * @uses Timespan::getDifference()
     * @return integer
     */
    function getDaysRemainder()
    {
        return $this->fullremdays;
    }

    /**
     * returns array with difference between the two dates
     *
     * differenz = array(
     *   fullremyears,
     *   fullremmonths,
     *   fullremdays,
     *   years,
     *   months,
     *   days, )
     *
     * @access public
     * @return array differences
     *
     */
    function getDifference()
    {
        return array(
            'fullremyears'  => $this->fullremyears,
            'fullremmonths' => $this->fullremmonths,
            'fullremdays'   => $this->fullremdays,
            'fullremhours'  => $this->fullremhours,
            'fullremminutes' => $this->fullremminutes,
            'fullremseconds' => $this->fullremseconds,
            'years'         => $this->years,
            'months'        => $this->months,
            'days'          => $this->days,
            'hours'         => $this->hours,
            'minutes'       => $this->minutes,
            'seconds'       => $this->seconds,
        );
    }
    
    /**
     * sets all properties back to 0
     */
    function resetProperties()
    {
        $this->fullremyears   = 0;
        $this->fullremmonths  = 0;
        $this->fullremdays    = 0;
        $this->fullremhours   = 0;
        $this->fullremminutes = 0;
        $this->fullremseconds = 0;
        $this->years          = 0;
        $this->months         = 0;
        $this->days           = 0;
        $this->hours          = 0;
        $this->minutes        = 0;
        $this->seconds        = 0;
    }
    
    /**
     * updates calculated values like days, months, years, ...
     */
    function updatePorperties()
    {
        $this->resetProperties();
        
        if ( ! is_a( $this->end, 'DateTime' ) || ! is_a( $this->start, 'DateTime' ) )
        {
            return;
        }
        
        if ( $this->end->getAsTs() === $this->start->getAsTs() )
        {
            return;
        }
        elseif ( $this->end->getAsTs() < $this->start->getAsTs() )
        {
            $positive = false;
        }
        else
        {
            $positive = true;
        }
        
        
        // Time
        $this->fullremseconds = $this->end->time->getSeconds() - $this->start->time->getSeconds();
        if ( $this->fullremseconds < 0 )
        {
            if ( $positive )
            {
                $this->fullremminutes--;
                $this->fullremseconds += 60;
            }
            else
            {
                $this->fullremminutes++;
                $this->fullremseconds -= 60;
            }
        }
        
        $this->fullremminutes += $this->end->time->getMinutes() - $this->start->time->getMinutes();
        if ( $this->fullremminutes < 0 )
        {
            if ( $positive )
            {
                $this->fullremhours--;
                $this->fullremminutes += 60;
            }
            else
            {
                $this->fullremhours++;
                $this->fullremminutes -= 60;
            }
        }
        
        $this->fullremhours += $this->end->time->getHours() - $this->start->time->getHours();
        if ( $this->fullremhours < 0 )
        {
            if ( $positive )
            {
                $this->fullremdays--;
                $this->fullremhours += 24;
            }
            else
            {
                $this->fullremdays++;
                $this->fullremhours -= 24;
            }
        }
        
        // Date
        $this->fullremdays += $this->end->date->getDay() - $this->start->date->getDay();
        if ( $this->fullremdays < 0 )
        {
            if ( $positive )
            {
                $this->fullremmonths--;
                $this->fullremdays = $this->start->date->getDaysInMonth() - $this->start->date->getDay();
                $this->fullremdays += $this->end->date->getDay();
            }
            else
            {
                $this->fullremmonths++;
                $this->fullremdays = $this->start->date->getDaysInMonth() - $this->start->date->getDay();
                $this->fullremdays -= $this->end->date->getDay();
            }
        }

        $this->fullremmonths += $this->end->date->getMonth() - $this->start->date->getMonth();
        if ( $this->fullremmonths < 0 )
        {
            if ( $positive )
            {
                $this->fullremyears--;
                $this->fullremmonths += 12;
            }
            else
            {
                $this->fullremyears++;
                $this->fullremmonths -= 12;
            }
        }

        $this->fullremyears += $this->end->date->getYear() - $this->start->date->getYear();

        
        $this->years    = $this->fullremyears;
        $this->months   = $this->fullremyears * 12 + $this->fullremmonths;
        
        $this->days     = floor( ( $this->end->date->getAsTs() - $this->start->date->getAsTs() ) / 60 / 60 / 24 );
        
        $this->hours    = $this->days * 24 + $this->fullremhours;
        $this->minutes  = $this->hours * 60 + $this->fullremminutes;
        $this->seconds  = $this->minutes * 60 + $this->fullremseconds;
    }
    
    /**
     * returns difference in seconds
     *
     * @return integer seconds
     */
    function getAsSeconds()
    {
        return $this->seconds;
    }
    
    /**
     * returns string with a textual representation of the time-difference
     *
     * @param string $lang
     * @return string
     */
    function getAsString( $lang = 'en' )
    {
        $locale_names['en']['second']   = ' second';
        $locale_names['en']['seconds']  = ' seconds';
        $locale_names['en']['minute']   = ' minute';
        $locale_names['en']['minutes']  = ' minutes';
        $locale_names['en']['hour']     = ' hour';
        $locale_names['en']['hours']    = ' hours';
        $locale_names['en']['day']      = ' day';
        $locale_names['en']['days']     = ' days';
        $locale_names['en']['week']     = ' week';
        $locale_names['en']['weeks']    = ' weeks';
        $locale_names['en']['month']    = ' month';
        $locale_names['en']['months']   = ' months';
        $locale_names['en']['year']     = ' year';
        $locale_names['en']['years']    = ' years';
        $locale_names['en']['and']      = ' and';
        
        $locale_names['de']['second']   = ' Sekunde';
        $locale_names['de']['seconds']  = ' Sekunden';
        $locale_names['de']['minute']   = ' Minute';
        $locale_names['de']['minutes']  = ' Minuten';
        $locale_names['de']['hour']     = ' Stunde';
        $locale_names['de']['hours']    = ' Stunden';
        $locale_names['de']['day']      = ' Tag';
        $locale_names['de']['days']     = ' Tage';
        $locale_names['de']['week']     = ' Woche';
        $locale_names['de']['weeks']    = ' Wochen';
        $locale_names['de']['month']    = ' Monat';
        $locale_names['de']['months']   = ' Monate';
        $locale_names['de']['year']     = ' Jahr';
        $locale_names['de']['years']    = ' Jahre';
        $locale_names['de']['and']      = ' und';
        
        $_ =& $locale_names[$lang];
        
        $this->updatePorperties();
        
        $time = array();
        
        if ( $difference['fullremdays'] > 1 )
        {
            $time[] = $difference['fullremdays'] . $_['days'];
        }
        elseif ( $difference['fullremdays'] == 1 )
        {
            $time[] = $difference['fullremdays'] . $_['day'];
        }
        
        if ( $difference['fullremhours'] > 1 )
        {
            $time[] = $difference['fullremhours'] . $_['hours'];
        }
        elseif ( $difference['fullremhours'] == 1 )
        {
            $time[] = $difference['fullremhours'] . $_['hour'];
        }
        
        if ( $difference['fullremminutes'] > 1 )
        {
            $time[] = $difference['fullremminutes'] . $_['minutes'];
        }
        elseif ( $difference['fullremminutes'] == 1 )
        {
            $time[] = $difference['fullremminutes'] . $_['minute'];
        }
        
        if ( $difference['fullremseconds'] > 1 )
        {
            $time[] = $difference['fullremseconds'] . $_['seconds'];
        }
        elseif ( $difference['fullremseconds'] == 1 )
        {
            $time[] = $difference['fullremseconds'] . $_['second'];
        }
        
        $time = implode( ', ', $time );
        if ( strstr( $time, ',' ) )
        {
            $time = substr_replace( $time, $_['and'], strrpos( $time, ',' ), 1 );
        }
        
        return $time;
    }
}
?>