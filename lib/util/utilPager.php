<?php
/**
* @author Arden Vallente <vallente@simplexi.com>
* @version 1.0
*
* utilPager
*/
class utilPager
{
	/**
	* @param int $iMax => total number of data,
	* @param int $iPageNum => current page number
	* @param int $iNumRow => number of row per page
	* @param string $sJs => javascript name function. function to be called
	* @param array $aParam(optional) => additional parameter passed to js function
	* @return string $sReturn => generated pager
	*
	* @implementation: 
	*	$iMax = 50;
	*	$iCurPage = 1;  	
	*	$iTotalRow = 5; //5 row per page
	*	$sJsFunction = 'goToFunction'; 
	*	$aParams = array('param1', 'param2');
	*	$sPage = utilPager::pager($iMax, $iCurPage, $iTotalRow, $sJsFunction, $aParams); 
	* @sample output: 
	*	1 2 3 ... 10
	*/
	public static function pager($iMax, $iPageNum, $iNumRow, $sJs, $aParam='')
	{
		$iTotalPage = ceil($iMax / $iNumRow);
        $first = 1;
        $last = $iTotalPage;
        
        if ($iPageNum-3 > $first) {
            $ctrStart = $iPageNum-2;
        } else {
            $ctrStart = $first;
        }
        
        if ($iPageNum+3 < $last) {
            $ctrEnd = $iPageNum+2;
        } else {
            $ctrEnd = $last;
        }
        
        $aPage = array();
        
        if ($ctrStart != $first) {
            $aPage[] = $first;
            $aPage[] = '...';
        }
        
        for ($i = $ctrStart; $i <= $ctrEnd; $i++) {
            $aPage[] = $i;
        }
        
        if ($ctrEnd != $last) {
            $aPage[] = '...';
            $aPage[] = $last;
        }

		$sParam = "";
		// Translate parameter to string.
		if (is_array($aParam) && $aParam != "") {
			foreach ($aParam as $i => $value) {
				$aParam[$i] = "'".addslashes($value)."'";
			}
			$sParam = ','.implode(",", $aParam);
		}

		$sReturn = '<ul class="pager">';
		foreach ($aPage as $val) {
			// Array value equal to ...
			if ($val == '...') {
				$sReturn .= '<li class="dots"><span>'.$val.'</span></li>';
			} else if ($val == $iPageNum) { 
			// Array value equal to current page
				$sReturn .= '<li><strong>'.$val.'</strong></li>';
			} else {
				$sReturn .= '<li><a href="'.$sJs.'('.$val.$sParam.')">'.$val.'</a></li>';
			}
		}
		$sReturn .= '</ul>';

		return $sReturn;
	}
}