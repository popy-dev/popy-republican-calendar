<?php

namespace Popy\RepublicanCalendar\Utility;

class TimeConverter
{
    /**
     * Converts a "Time" (represented by an array of each of its constituents)
     *     from one format (defined by constituents sizes) into another format
     *     (defined by constituents sizes).
     *
     * @param array $timeParts           Time constituents array.
     * @param array $sourceFractionSizes Source time constituants ranges.
     * @param array $targetFractionSizes Target time constituants ranges.
     * 
     * @return array
     */
    public function convertTime(array $timeParts, array $sourceFractionSizes, array $targetFractionSizes)
    {
        return $this->convertTimeWithLowerUnityCountRatio($timeParts, $sourceFractionSizes, $targetFractionSizes);
        //return $this->convertTimeWithDayFraction($timeParts, $sourceFractionSizes, $targetFractionSizes);
    }

    /**
     * Converts a "Time" (represented by an array of each of its constituents)
     *     from one format (defined by constituents sizes) into another format
     *     (defined by constituents sizes) using a fraction of day calculation.
     *
     * @param array $timeParts           Time constituents array.
     * @param array $sourceFractionSizes Source time constituants ranges.
     * @param array $targetFractionSizes Target time constituants ranges.
     * 
     * @return array
     */
    public function convertTimeWithDayFraction(array $timeParts, array $sourceFractionSizes, array $targetFractionSizes)
    {
        return $this->getTimeFromDayFraction(
            $this->getDayFractionFromTime($timeParts, $sourceFractionSizes),
            $targetFractionSizes
        );
    }

    /**
     * Converts a "Time" (represented by an array of each of its constituents)
     *     into a fraction of a day, based on the constituents ranges.
     * 
     * @param array $timeParts     Time constituents array.
     * @param array $fractionSizes Time constituants ranges.
     *
     * @return float
     */
    public function getDayFractionFromTime(array $timeParts, array $fractionSizes)
    {
        $len = count($fractionSizes);
        $fraction = 0;

        for ($i = count($timeParts) - 1; $i > -1; $i--) {
            $part = isset($timeParts[$i]) ? $timeParts[$i] : 0;
            $fraction = ($fraction + $part) / $fractionSizes[$i];
        }

        return $fraction;
    }

    /**
     * Converts a dayFraction onto a "Time" (represented by an array of each of
     *     its constituents) based on the constituents ranges.
     *
     * @param float $dayFraction   Day fraction.
     * @param array $fractionSizes Time constituants ranges.
     *
     * @return array
     */
    public function getTimeFromDayFraction($dayFraction, array $fractionSizes)
    {
        $res = [];
        $len = count($fractionSizes);

        for ($i=0; $i < $len; $i++) { 
            $dayFraction = $dayFraction * $fractionSizes[$i];

            if ($i + 1 < $len) { 
                $dayFraction = $dayFraction - ($res[] = (int)$dayFraction);
            } else {
                // Rounding last value to avoid loosing data
                $res[] = round($dayFraction);
            }
        }

        for ($i=$len-1; $i > -1 ; $i--) { 
            if ($res[$i] < $fractionSizes[$i]) {
                // everything is fine.
                break;
            }

            // A rounding got us over limit
            if ($i) {
                $res[$i] -= $fractionSizes[$i];
                $res[$i-1]++;
            }
        }

        // Possible issue : the heaviest time component could have reached it's
        // upper limit, reaching the next day. It could cause an issue depending
        // on how the time is set in the final object.
        // 
        // Usually this issue will only happen if reconverting a Republican date
        // into a conventional Date, and native implementations handle it well.

        return $res;
    }

    /**
     * Converts a "Time" (represented by an array of each of its constituents)
     *     from one format (defined by constituents sizes) into another format
     *     (defined by constituents sizes) using a "lower unity count ratio"
     *     calculation.
     *
     * @param array $timeParts           Time constituents array.
     * @param array $sourceFractionSizes Source time constituants ranges.
     * @param array $targetFractionSizes Target time constituants ranges.
     * 
     * @return array
     */
    public function convertTimeWithLowerUnityCountRatio(array $timeParts, array $sourceFractionSizes, array $targetFractionSizes)
    {
        $count = $this->getLowerUnityCountFromTime($timeParts, $sourceFractionSizes);

        // Now we multiply the count by the total units per day of the target
        //    format, then divide by the total units per date of the source
        //    format, so we only have one division, one transformation to float,
        //    so only one "float imprecision" issue.
        $count = ($count * array_product($targetFractionSizes))
            / array_product($targetFractionSizes)
        ;

        return $this->getTimeFromLowerUnityCount($count, $targetFractionSizes);
    }

    /**
     * Converts a "Time" (represented by an array of each of its constituents)
     *     into the lowest of its defined units (usefull if you want, for
     *     instance, to convert a [h,m,s,u] into seconds)
     * 
     * @param array $timeParts     Time constituents array.
     * @param array $fractionSizes Time constituants ranges.
     *
     * @return integer
     */
    public function getLowerUnityCountFromTime(array $timeParts, array $fractionSizes)
    {
        $len = count($fractionSizes);
        $res = 0;
   
        for ($i=0; $i < $len; $i++) {
            $part = isset($timeParts[$i]) ? $timeParts[$i] : 0;
            $res = $res * $fractionSizes[$i] + $part;
        }

        return $res;
    }

    /**
     * Convert a "lowest unity count" time representation to an array of
     *     constituents.
     *
     * @param integer $count         Lowest unity count.
     * @param array   $fractionSizes Time constituants ranges.
     *
     * @return array
     */
    public function getTimeFromLowerUnityCount($count, array $fractionSizes)
    {
        $len = count($fractionSizes);
        $res = array_fill(0, $len, 0);

        for ($i=$len - 1; $i > -1 ; $i--) { 
            $res[$i] = $count % $fractionSizes[$i];
            $count = intval($count / $fractionSizes[$i]);
        }

        return $res;
    }
}