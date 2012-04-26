<?php

$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once($DOCUMENT_ROOT."/lib/util/utilFileLogger.php");

/**
 * utilFileUploader
 * 파일 업로드  관련 라이브러리 클래스
 * - upload
 *
 * <code>
 * <?php
 *
 * $aFile = getParam('upload_file');
 *
 * // 일반 파일 업로드
 * $oUploader = new utilFileUploader();
 * $mUploadFilePath = $oUploader->upload($aFile,$업로드 경로);
 * // $mUploadFilePath = Upload Path
 * // $mUploadFileUri = Upload File Uri
 * $mUploadFileUri = $oUploader->getFilePathToUri($mUploadFilePath);
 *
 * // 업로드 불가능한 파일 확장자 인지 확인
 * $helpLibFileFileUpload->isBanExtension($확장자);
 *
 * ?>
 * </code>
 *
 * @package      helper
 * @subpackage   helperLibFileUpload
 * @author       YongHun Jeong <yhjeong@simplexi.com>
 * @since        2010. 11. 16.
 * @version      1.0
 *
 */
class utilFileUploader
{
    /**
     * @var $_aBanExtention 업로드 금지 확장자
     */
    protected $_aBanExtention = array('sh','php','phtm','cgi','pl','py','html','htm','xml','inc', 'shtml', 'stm', 'hta', 'tpl', 'php3', 'pm', 'pyw');

    /**
     * @var $_aExtraBanExtention 업로드 확장자를 추가할경우
     */
    protected $_aExtraBanExtention;

    /**
     * @var $_aAllowExtention 특정 확장자만 허용할경우
     */
    protected $_aAllowExtention;

    /**
     *
     * @var utilFile
     */
    private $oCObject;

    /**
     * upload
     * 파일 업로드
     *
     * @package      helper
     * @subpackage   helperLibFileUpload
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        array      $aFile  upload file array
     * @param        string     $sUploadDir  upload file path
     * @return       mixed      $sResfile  Success : string Upload File Full Path , Failure : boolean false
     *
     *
     */
    public function upload($aFile, $sUploadDir = "" , $bAllowMode = FALSE)
    {
        # File Size Check
        if ( !$aFile['size'] ) {
            return false;
        } else {
            // Upload Limit size
            if($this->isLimitUploadSize($aFile['size'])) return false;
        }

        $sUploadPath = PLUGIN_UPLOAD_PATH.date('Y').DS.date('m').DS.date('d').DS.$sUploadDir;

        #업로드 디렉토리가 없다면 생성
        if ( !is_dir($sUploadPath) ) {
            $this->makedir($sUploadPath, CHMOD_USER , true);
        }

        $sFile = $aFile['tmp_name'];
        $sFilename = $aFile['name'];

        if ( !is_uploaded_file( $sFile ) ) return false;

        #순수 파일명
        $sName = $this->getFileName($sFilename);

        #확장자
        $sExtension = $this->getFileExtension($sFilename);

        #업로드 금지된 확장자인지 확인
        if ($bAllowMode) {
            if (!$this->isNotAllowExtention($sExtension)) return false;
        } else {
            if ($this->isBanExtension($sExtension)) return false;
        }

        #저장될 경로
        if ($sExtension) {
            $sResfile = $sUploadPath.sha1(time().$sName).'.'.$sExtension;
        } else {
            $sResfile = $sUploadPath.sha1(time().$sName);
        }

        #중복 파일명 재할당
        if (file_exists($sResfile)) {
            $i = 0;

            while (true) {
                if ($sExtension) {
                    $sResfile = $sUploadPath.sha1($sName.$i).'.'.$sExtension;
                } else {
                    $sResfile = $sUploadPath.sha1($sName.$i);
                }

                if( !file_exists($sResfile) ) break;
                $i++;
            }
        }

        if ( !move_uploaded_file( $sFile, $sResfile ) ) return false;

        #로그
        $this->_logger()->log($sResfile, "i");

        return $sResfile;
    }

    /**
     *
     * Return To File Uri
     */
    public function getFilePathToUri ($sFilePath)
    {
        $sFileUri = str_replace(SERVER_DOCUMENT_ROOT, "", $sFilePath);
        $sFileUri = str_replace(DS, "/", $sFileUri);
        return $sFileUri;
    }

    /**
     * origin_upload
     * 파일 업로드
     *
     * @package      helper
     * @subpackage   helperLibFileUpload
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        array      $aFile  upload file array
     * @param        string     $sUploadDir  upload file path
     * @return       mixed      $sResfile  Success : string Upload File Full Path , Failure : boolean false
     *
     *
     */
    public function origin_upload($aFile, $sUploadDir="" , $bAllowMode = FALSE)
    {
        # File Size Check
        if ( !$aFile['size'] ) {
            d('File Upload Failure : [RE] upload File Size is Empty');
            return false;
        } else {
            // Upload Limit size
            if($this->isLimitUploadSize($aFile['size'])) return false;
        }

        $sUploadPath = PLUGIN_UPLOAD_PATH.DS.date('Y').DS.date('m').DS.date('d').DS.$sUploadDir;

        #업로드 디렉토리가 없다면 생성
        if ( !is_dir($sUploadPath) ) {
            $this->makedir($sUploadPath , CHMOD_USER , true);
        }

        $sFile = $aFile['tmp_name'];
        $sFilename = $aFile['name'];

        if ( !is_uploaded_file( $sFile ) ) return false;

        #순수 파일명
        $sName = $this->getFileName($sFilename);

        #확장자
        $sExtension = $this->getFileExtension($sFilename);

        #업로드 금지된 확장자인지 확인
        if ($bAllowMode) {
            if (!$this->isNotAllowExtention($sExtension)) return false;
        } else {
            if ($this->isBanExtension($sExtension)) return false;
        }



        #저장될 경로
        //FIXME 한글파일명 깨짐 처리 필요
        //FIXME 공백 치환 정책 필요
        $sName = str_replace(" ", "_", $sName);

        if ($sExtension) {
            $sResfile = $sUploadPath.$sName.'.'.$sExtension;
        } else {
            $sResfile = $sUploadPath.$sName;
        }

        #중복 파일명 재할당
        //FIXME 중복파일명 정책 필요 = 현재는 return false

        $iCount = 0;

        $bFlag = true;
        $sChangeName = $sName;
        for($i=1; $bFlag; $i++){
            if (file_exists($sResfile)){
                $sName = $sChangeName.'_'.$i;

                $sResfile = $sUploadPath.$sName.'.'.$sExtension;
            } else {
                $bFlag = false;
            }
        }
        if ( !move_uploaded_file( $sFile, $sResfile ) ) {
            return false;
        }
        #로그
        $this->_logger()->log($sResfile, "i");

        return $sName.'.'.$sExtension;
    }

    /**
     * php_ini의 upload_max_filesize 와 비교해서 upload허용여부 결정
     * @param integer $iFileSize
     * @return boolean true : Upload Limit Size Over , invalid argument false : passed
     */
    public function isLimitUploadSize($iFileSize)
    {
        if($this->isNullAndEmpty($iFileSize)) return true;

        $iLimitUploadSize = ini_get('upload_max_filesize');

        $iLimitUploadSize = $this->getReverseFormatFileSize($iLimitUploadSize);

        if ($iLimitUploadSize <= $iFileSize) {
            d('File Upload Limit Size : '.$iLimitUploadSize .' , Request Size : '.$iFileSize .' Size Limit Over');
            return true;
        } else return false;
    }

    /**
     * isNotAllowExtention
     * 허용된 확장자인지 체크합니다.
     *
     * @package      helper
     * @subpackage   helperLibFileUpload
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 18.
     * @version      1.0
     *
     * @param        string     $sExtension  확장자
     * @return       boolean    금지된 확장자일 경우에만 true , 빈값 혹은 그외의 경우에는 false를 반환한다.
     *
     */
    public function isNotAllowExtention ($sExtension)
    {
        if($this->isNullAndEmpty($sExtension)) return false;

        if(empty($this->_aAllowExtention)) return false;

        $sMatcher = implode('|', $this->_aAllowExtention);

        if ( preg_match('/('.$sMatcher.')$/i', $sExtension) ) return true;
        else {
            //d('['.$sExtension.'] 허용하지 않는 파일 확장자입니다. 허용하w 확장자 리스트는  요레~요레~ [.|..|'.$sMatcher.']');
            return false;
        }
    }


    /**
     * isBanExtension
     * 금지된 파일 확장자인지 체크 합니다.
     *
     * @package      helper
     * @subpackage   helperLibFileUpload
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 18.
     * @version      1.0
     *
     * @param        string     $sExtension  확장자
     * @return       boolean    금지된 확장자일 경우에만 true , 빈값 혹은 그외의 경우에는 false를 반환한다.
     *
     */
    public function isBanExtension ($sExtension , $bSpecificExtentionCheck = FALSE)
    {

        if ( $bSpecificExtentionCheck ) {
            $aCheckExtention = array_merge($this->_aBanExtention , $this->_aExtraBanExtention);

        } else $aCheckExtention =  $this->_aBanExtention;

        $sMatcher = implode('|', $aCheckExtention);

        if ( preg_match('/('.$sMatcher.')$/i', $sExtension) ) {
           // d('['.$sExtension.'] 금지된 파일 확장자입니다. 금지하는 확장자 목록은 요레~요레~ [.|..|'.$sMatcher.']');
            return true;
        } else  return false;
    }

    /**
     * 금지하는 확장자를 더 추가할경우
     * @param mixed $mBanExtention (Array or String)
     */
    public function setAddBanExtention ($mBanExtention)
    {
        #validation
        if( $this->isNullAndEmpty($mBanExtention) ) return false;

        $sMatcher = implode('|', $this->_aBanExtention);

        if (is_array($mBanExtention)) {

            foreach ($mBanExtention as $v) {
                if ( preg_match('/('.$sMatcher.')$/i', $v) ) {
                   // d('['.$v.'] 확장자는 이미 존재하는 확장자 입니다. ['.$sMatcher.']');
                    continue;
                } else $this->_aExtraBanExtention[] = $v;
            }

        } else {

            if ( preg_match('/('.$sMatcher.')$/i', $mBanExtention) ) {
               // d('['.$mBanExtention.'] 확장자는 이미 존재하는 확장자 입니다. ['.$sMatcher.']');
            } else $this->_aExtraBanExtention[] = $mBanExtention;

        }
    }

    /**
     * 특정 파일만 업로드 할경우
     * @param mixed $mAllowExtention 추가할 특정확장자
     */
    public function setAllowSpecificExtention ($mAllowExtention )
    {
        if (is_array($mAllowExtention)) foreach ($mAllowExtention as $v) $this->_aAllowExtention[] = $v;
        else $this->_aAllowExtention[] = $mAllowExtention;
    }


    /**
     * isUploaded
     * 업로드된   파일이 있는지 리턴
     *
     * @package      helper
     * @subpackage   helperLibFileUpload
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 18.
     * @version      1.0
     *
     * @return       boolean     true : 업로드된 파일 내역이 있음 , false : 없음
     *
     *
     */
    public function isUploaded($aFile)
    {
        if (is_uploaded_file($aFile['tmp_name'])) return true;
        else return false;
    }

    /**
     * getUploadFileSize
     * 업로드 용량
     *
     * @package      helper
     * @subpackage   helperLibFileUpload
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 18.
     * @version      1.0
     *
     * @param        array      $aFile  용량 체크 대상 파일
     * @return       string     $sFileSize  용량 포멧 첨부 크기
     *
     *
     */
    public function getUploadFileSize($aFile , $sFormatFlag = true)
    {
        if ( !$aFile['size'] ) return false;
        if ($sFormatFlag) $sFileSize = $this->getFileSizeFormat($aFile['size']);
        else $sFileSize = $aFile['size'].' Byte';
        return $sFileSize;
    }

	/**
     * getFileExtention
     *
     * 파일 확장자 반환
     *
     *
     * @package      helper
     * @subpackage   helperLibFile
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        string     $sFileName  파일명
     * @return       string     $sExtensionName  확장자명
     *
     */
    public function getFileExtension($sFileName)
    {
        if($this->isNullAndEmpty($sFileName)) return false;

        $sExtensionName = strrchr($sFileName, '.');
        $sExtensionName = str_replace('.', '', $sExtensionName);

        if($this->isNullAndEmpty($sExtensionName)) return false;

        return $sExtensionName;
    }


    /**
     * isNullAndEmpty
     * null 혹은 empty 체크 함수 추출
     *
     * @package      helper
     * @subpackage   helperLibFile
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        string     $sTarget  타겟string
     * @return       boolean true : null or empty , false : normal
     *
     */
    protected function isNullAndEmpty($sTarget,$sMessage = 'Target')
    {
        if (empty($sTarget)) {
            return true;
        }

        if (is_null($sTarget)) {
            return true;
        }

        return false;
    }

    /**
     * getFileName
     * 확장자를 제외한 파일 이름 반환
     *
     * @package      helper
     * @subpackage   helperLibFile
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        string     $sFileName  파일명
     * @return       string     $sFileName  확장자를 제외한 파일 이름
     *
     */
    public function getFileName($sFileName)
    {
        if($this->isNullAndEmpty($sFileName)) return false;

        $sFileName = preg_replace('@.*/@', '', $sFileName);

        $iIdx = strrpos($sFileName, '.');
        $sFileName = substr($sFileName, 0, $iIdx);

        if($this->isNullAndEmpty($sFileName)) return false;

        return $sFileName;
    }


    /**
     * getFileSizeFormat
     * 파일 사이즈 포멧 반환
     *
     * @package      helper
     * @subpackage   helperLibFile
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        integer    $iBytes  DESCRIPTION
     * @param        string     $sText  DESCRIPTION
     * @return       string     $sFileSizeFormat  DESCRIPTION
     *
     *
     */
    public function getFileSizeFormat($iBytes, $sText='yte')
    {
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = 1099511627776;

        $iRound = 2;

        if (($iBytes < $kb) || $type == 'b') {
            $sRs = number_format($iBytes)." B";
        } elseif ($iBytes < $mb) {
            $sRs = number_format(round($iBytes / $kb, $iRound))." KB";
        } elseif ($iBytes < $gb) {
            $sRs = number_format(round($iBytes / $mb, $iRound))." MB";
        } elseif ($iBytes < $tb) {
            $sRs = number_format(round($iBytes / $gb, $iRound), $iRound)." GB";
        } else {
            $sRs = number_format(round($iBytes / $tb, $iRound), $iRound)." TB";
        }

        $sFileSizeFormat = $sRs.$sText;

        return $sFileSizeFormat;
    }


    /**
     * php.ini 의 upload_file_size 기준 M , m 형식의 포멧을 원래 byte로 돌려준다.
     * @param integer $sByte
     */
    public function getReverseFormatFileSize($sByte)
    {
        $kb = 1024;
        $mb = 1048576;        // 1024 * 1024
        $gb = 1073741824;     // 1024 * 1024 * 1024
        $tb = 1099511627776;  // 1024 * 1024 * 1024 * 1024

        switch (substr ($sByte, -1)) {
            case 'M': case 'm': return (int)$sByte * $mb;
            case 'K': case 'k': return (int)$sByte * $kb;
            case 'G': case 'g': return (int)$sByte * $gb;
            case 'T': case 't': return (int)$sByte * $gb;
            default: return $sByte;
        }
    }



    /**
     * getFileMimeType
     * 파일의 Mine Type 반환
     *
     * @package      helper
     * @subpackage   helperLibFile
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        string     $sName  파일명
     * @return       string     $sFileMimeType  mimetype
     *
     *
     */
    public function getFileMimeType($sName)
    {
        $aType = array (
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/mpeg',
            'wma' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $sExt = strtolower(array_pop(explode('.',$sName)));


        if (array_key_exists($sExt, $aType)) {
            $sFileMimeType = $aType[$sExt];
        } else {
            $sFileMimeType = 'application/octet-stream';
        }

        return $sFileMimeType;
    }

    /**
     *
     * TODO getExtensionByMimeType
     * @param unknown_type $sMimeType
     */
    public function getExtensionByMimeType($sMimeType)
    {
           /*
            application/envoy	evy
            application/fractals	fif
            application/futuresplash	spl
            application/hta	hta
            application/internet-property-stream	acx
            application/mac-binhex40	hqx
            application/msword	doc
            application/msword	dot
            application/octet-stream	*
            application/octet-stream	bin
            application/octet-stream	class
            application/octet-stream	dms
            application/octet-stream	exe
            application/octet-stream	lha
            application/octet-stream	lzh
            application/oda	oda
            application/olescript	axs
            application/pdf	pdf
            application/pics-rules	prf
            application/pkcs10	p10
            application/pkix-crl	crl
            application/postscript	ai
            application/postscript	eps
            application/postscript	ps
            application/rtf	rtf
            application/set-payment-initiation	setpay
            application/set-registration-initiation	setreg
            application/vnd.ms-excel	xla
            application/vnd.ms-excel	xlc
            application/vnd.ms-excel	xlm
            application/vnd.ms-excel	xls
            application/vnd.ms-excel	xlt
            application/vnd.ms-excel	xlw
            application/vnd.ms-outlook	msg
            application/vnd.ms-pkicertstore	sst
            application/vnd.ms-pkiseccat	cat
            application/vnd.ms-pkistl	stl
            application/vnd.ms-powerpoint	pot
            application/vnd.ms-powerpoint	pps
            application/vnd.ms-powerpoint	ppt
            application/vnd.ms-project	mpp
            application/vnd.ms-works	wcm
            application/vnd.ms-works	wdb
            application/vnd.ms-works	wks
            application/vnd.ms-works	wps
            application/winhlp	hlp
            application/x-bcpio	bcpio
            application/x-cdf	cdf
            application/x-compress	z
            application/x-compressed	tgz
            application/x-cpio	cpio
            application/x-csh	csh
            application/x-director	dcr
            application/x-director	dir
            application/x-director	dxr
            application/x-dvi	dvi
            application/x-gtar	gtar
            application/x-gzip	gz
            application/x-hdf	hdf
            application/x-internet-signup	ins
            application/x-internet-signup	isp
            application/x-iphone	iii
            application/x-javascript	js
            application/x-latex	latex
            application/x-msaccess	mdb
            application/x-mscardfile	crd
            application/x-msclip	clp
            application/x-msdownload	dll
            application/x-msmediaview	m13
            application/x-msmediaview	m14
            application/x-msmediaview	mvb
            application/x-msmetafile	wmf
            application/x-msmoney	mny
            application/x-mspublisher	pub
            application/x-msschedule	scd
            application/x-msterminal	trm
            application/x-mswrite	wri
            application/x-netcdf	cdf
            application/x-netcdf	nc
            application/x-perfmon	pma
            application/x-perfmon	pmc
            application/x-perfmon	pml
            application/x-perfmon	pmr
            application/x-perfmon	pmw
            application/x-pkcs12	p12
            application/x-pkcs12	pfx
            application/x-pkcs7-certificates	p7b
            application/x-pkcs7-certificates	spc
            application/x-pkcs7-certreqresp	p7r
            application/x-pkcs7-mime	p7c
            application/x-pkcs7-mime	p7m
            application/x-pkcs7-signature	p7s
            application/x-sh	sh
            application/x-shar	shar
            application/x-shockwave-flash	swf
            application/x-stuffit	sit
            application/x-sv4cpio	sv4cpio
            application/x-sv4crc	sv4crc
            application/x-tar	tar
            application/x-tcl	tcl
            application/x-tex	tex
            application/x-texinfo	texi
            application/x-texinfo	texinfo
            application/x-troff	roff
            application/x-troff	t
            application/x-troff	tr
            application/x-troff-man	man
            application/x-troff-me	me
            application/x-troff-ms	ms
            application/x-ustar	ustar
            application/x-wais-source	src
            application/x-x509-ca-cert	cer
            application/x-x509-ca-cert	crt
            application/x-x509-ca-cert	der
            application/ynd.ms-pkipko	pko
            application/zip	zip
            audio/basic	au
            audio/basic	snd
            audio/mid	mid
            audio/mid	rmi
            audio/mpeg	mp3
            audio/x-aiff	aif
            audio/x-aiff	aifc
            audio/x-aiff	aiff
            audio/x-mpegurl	m3u
            audio/x-pn-realaudio	ra
            audio/x-pn-realaudio	ram
            audio/x-wav	wav
            image/bmp	bmp
            image/cis-cod	cod
            image/gif	gif
            image/ief	ief
            image/jpeg	jpe
            image/jpeg	jpeg
            image/jpeg	jpg
            image/pipeg	jfif
            image/png	png
            image/svg+xml	svg
            image/tiff	tif
            image/tiff	tiff
            image/x-cmu-raster	ras
            image/x-cmx	cmx
            image/x-icon	ico
            image/x-portable-anymap	pnm
            image/x-portable-bitmap	pbm
            image/x-portable-graymap	pgm
            image/x-portable-pixmap	ppm
            image/x-rgb	rgb
            image/x-xbitmap	xbm
            image/x-xpixmap	xpm
            image/x-xwindowdump	xwd
            message/rfc822	mht
            message/rfc822	mhtml
            message/rfc822	nws
            text/css	css
            text/h323	323
            text/html	htm
            text/html	html
            text/html	stm
            text/iuls	uls
            text/plain	bas
            text/plain	c
            text/plain	h
            text/plain	txt
            text/richtext	rtx
            text/scriptlet	sct
            text/tab-separated-values	tsv
            text/webviewhtml	htt
            text/x-component	htc
            text/x-setext	etx
            text/x-vcard	vcf
            video/mpeg	mp2
            video/mpeg	mpa
            video/mpeg	mpe
            video/mpeg	mpeg
            video/mpeg	mpg
            video/mpeg	mpv2
            video/quicktime	mov
            video/quicktime	qt
            video/x-la-asf	lsf
            video/x-la-asf	lsx
            video/x-ms-asf	asf
            video/x-ms-asf	asr
            video/x-ms-asf	asx
            video/x-msvideo	avi
            video/x-sgi-movie	movie
            x-world/x-vrml	flr
            x-world/x-vrml	vrml
            x-world/x-vrml	wrl
            x-world/x-vrml	wrz
            x-world/x-vrml	xaf
            x-world/x-vrml	xof
            */
        return false;
    }


    /**
     *
     * TODO getMimeTypeByFileExtension
     * @param unknown_type $sExtension
     */
    public function getMimeTypeByFileExtension($sExtension)
    {
           /*
             	'default' : application/octet-stream
                323	text/h323
                acx	application/internet-property-stream
                ai	application/postscript
                aif	audio/x-aiff
                aifc	audio/x-aiff
                aiff	audio/x-aiff
                asf	video/x-ms-asf
                asr	video/x-ms-asf
                asx	video/x-ms-asf
                au	audio/basic
                avi	video/x-msvideo
                axs	application/olescript
                bas	text/plain
                bcpio	application/x-bcpio
                bin	application/octet-stream
                bmp	image/bmp
                c	text/plain
                cat	application/vnd.ms-pkiseccat
                cdf	application/x-cdf
                cer	application/x-x509-ca-cert
                class	application/octet-stream
                clp	application/x-msclip
                cmx	image/x-cmx
                cod	image/cis-cod
                cpio	application/x-cpio
                crd	application/x-mscardfile
                crl	application/pkix-crl
                crt	application/x-x509-ca-cert
                csh	application/x-csh
                css	text/css
                dcr	application/x-director
                der	application/x-x509-ca-cert
                dir	application/x-director
                dll	application/x-msdownload
                dms	application/octet-stream
                doc	application/msword
                dot	application/msword
                dvi	application/x-dvi
                dxr	application/x-director
                eps	application/postscript
                etx	text/x-setext
                evy	application/envoy
                exe	application/octet-stream
                fif	application/fractals
                flr	x-world/x-vrml
                gif	image/gif
                gtar	application/x-gtar
                gz	application/x-gzip
                h	text/plain
                hdf	application/x-hdf
                hlp	application/winhlp
                hqx	application/mac-binhex40
                hta	application/hta
                htc	text/x-component
                htm	text/html
                html	text/html
                htt	text/webviewhtml
                ico	image/x-icon
                ief	image/ief
                iii	application/x-iphone
                ins	application/x-internet-signup
                isp	application/x-internet-signup
                jfif	image/pipeg
                jpe	image/jpeg
                jpeg	image/jpeg
                jpg	image/jpeg
                js	application/x-javascript
                latex	application/x-latex
                lha	application/octet-stream
                lsf	video/x-la-asf
                lsx	video/x-la-asf
                lzh	application/octet-stream
                m13	application/x-msmediaview
                m14	application/x-msmediaview
                m3u	audio/x-mpegurl
                man	application/x-troff-man
                mdb	application/x-msaccess
                me	application/x-troff-me
                mht	message/rfc822
                mhtml	message/rfc822
                mid	audio/mid
                mny	application/x-msmoney
                mov	video/quicktime
                movie	video/x-sgi-movie
                mp2	video/mpeg
                mp3	audio/mpeg
                mpa	video/mpeg
                mpe	video/mpeg
                mpeg	video/mpeg
                mpg	video/mpeg
                mpp	application/vnd.ms-project
                mpv2	video/mpeg
                ms	application/x-troff-ms
                mvb	application/x-msmediaview
                nws	message/rfc822
                oda	application/oda
                p10	application/pkcs10
                p12	application/x-pkcs12
                p7b	application/x-pkcs7-certificates
                p7c	application/x-pkcs7-mime
                p7m	application/x-pkcs7-mime
                p7r	application/x-pkcs7-certreqresp
                p7s	application/x-pkcs7-signature
                pbm	image/x-portable-bitmap
                pdf	application/pdf
                pfx	application/x-pkcs12
                pgm	image/x-portable-graymap
                pko	application/ynd.ms-pkipko
                pma	application/x-perfmon
                pmc	application/x-perfmon
                pml	application/x-perfmon
                pmr	application/x-perfmon
                pmw	application/x-perfmon
                pnm	image/x-portable-anymap
                pot,	application/vnd.ms-powerpoint
                ppm	image/x-portable-pixmap
                pps	application/vnd.ms-powerpoint
                ppt	application/vnd.ms-powerpoint
                prf	application/pics-rules
                ps	application/postscript
                pub	application/x-mspublisher
                qt	video/quicktime
                ra	audio/x-pn-realaudio
                ram	audio/x-pn-realaudio
                ras	image/x-cmu-raster
                rgb	image/x-rgb
                rmi	audio/mid
                roff	application/x-troff
                rtf	application/rtf
                rtx	text/richtext
                scd	application/x-msschedule
                sct	text/scriptlet
                setpay	application/set-payment-initiation
                setreg	application/set-registration-initiation
                sh	application/x-sh
                shar	application/x-shar
                sit	application/x-stuffit
                snd	audio/basic
                spc	application/x-pkcs7-certificates
                spl	application/futuresplash
                src	application/x-wais-source
                sst	application/vnd.ms-pkicertstore
                stl	application/vnd.ms-pkistl
                stm	text/html
                svg	image/svg+xml
                sv4cpio	application/x-sv4cpio
                sv4crc	application/x-sv4crc
                swf	application/x-shockwave-flash
                t	application/x-troff
                tar	application/x-tar
                tcl	application/x-tcl
                tex	application/x-tex
                texi	application/x-texinfo
                texinfo	application/x-texinfo
                tgz	application/x-compressed
                tif	image/tiff
                tiff	image/tiff
                tr	application/x-troff
                trm	application/x-msterminal
                tsv	text/tab-separated-values
                txt	text/plain
                uls	text/iuls
                ustar	application/x-ustar
                vcf	text/x-vcard
                vrml	x-world/x-vrml
                wav	audio/x-wav
                wcm	application/vnd.ms-works
                wdb	application/vnd.ms-works
                wks	application/vnd.ms-works
                wmf	application/x-msmetafile
                wps	application/vnd.ms-works
                wri	application/x-mswrite
                wrl	x-world/x-vrml
                wrz	x-world/x-vrml
                xaf	x-world/x-vrml
                xbm	image/x-xbitmap
                xla	application/vnd.ms-excel
                xlc	application/vnd.ms-excel
                xlm	application/vnd.ms-excel
                xls	application/vnd.ms-excel
                xlt	application/vnd.ms-excel
                xlw	application/vnd.ms-excel
                xof	x-world/x-vrml
                xpm	image/x-xpixmap
                xwd	image/x-xwindowdump
                z	application/x-compress
                zip	application/zip
            */
        return false;
    }

    /**
     *
     * ZipFile Check
     * @param unknown_type $sType
     */
    public function getValidateTypeZip($sType)
    {
        $aZipFileType = array(
           'application/zip',
           'application/x-zip',
           'application/x-zip-compressed',
           'application/octet-stream',
           'application/x-compress',
           'application/x-compressed',
           'multipart/x-zip'
        );
        $aSearchKey = array_search($sType,$aZipFileType);

        return ($aSearchKey) ? true : false;
    }


    /**
     * getReadDir
     * 디렉토리를 읽어서 배열로 반환
     *
     * @package      newBuilder libCommon
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        string     $sDirName  Read Target Directory
     * @param        string     $sFilter  정규식 - match 되는것만 결과 array에 포함
     * @return       array      $aResult 디렉토리 내용
     *
     *
     */
    public function getReadDir($sDirName, $sFilter = '')
    {
        $sFilter = trim($sFilter);
        $aResult = array();

        $opendir = @opendir( @$sDirName );

        while ( $filelist = @readdir( $opendir ) ) {

            #숨김파일의 경우 무시
            if ( preg_match("@^\.@", $filelist) !== 0 ) continue;

            $path = $sDirName.'/'.$filelist;

            if (filetype($path) == 'dir') {

                $aResult = array_merge($aResult, self::getReadDir($path, $sFilter));

            } else if (filetype($path) == 'file') {

                #필터가 지정되어 있는 경우는, 매치되는 것만 배열에 할당시킴
                if ($sFilter && (preg_match($sFilter, $filelist) === 0) ) {
                    continue;
                }
                $aResult[] = $path;
            }
        }
        @closedir( @$opendir );
        return $result;
    }


    /**
     * getCopyDir
     * 디렉토리 복사
     *
     * @package      helper
     * @subpackage   helperLibFile
     * @author       YongHun Jeong <yhjeong@simplexi.com>
     * @since        2010. 11. 15.
     * @version      1.0
     *
     * @param        string     $sOrigin  복사할 원본 디렉토리
     * @param        string     $sTarget  복사될 목표 디렉토리
     * @return       boolean    $bResult  복사 결과
     *
     *
     */
    public function getCopyDir($sOrigin, $sTarget)
    {
        $bResult = true;

        if (!file_exists($sOrigin)) {
            return $bResult;
        }

        $this->makeDir($sTarget, CHMOD_USER , true);

        if ($dh = opendir($sOrigin)) {
            while (($file = readdir($dh)) !== false) {

                if ( $file == '.' || $file == '..' ) {
                    continue;
                }
                #svn 파일의 경우 무시
                if( preg_match("@^\.svn@", $file) !== 0 ) continue;

                $sOriginPath = $sOrigin.'/'.$file;
                $sTargetPath = $sTarget.'/'.$file;

                $sType =filetype($sOriginPath);

                if ($sType == "dir") {

                    $this->makeDir($sTarget, CHMOD_USER);
                    $bResult = self::getCopyDir($sOriginPath, $sTargetPath);

                } else if ($sType == "file") {

                    $bResult = copy($sOriginPath, $sTargetPath);

                }

                if( $bResult === false ) return $bResult;

            }
        }
        @closedir($sOrigin);
        return $bResult;
    }

    /**
     * move file (add logging)
     */
    public function move($sOldFilename, $sNewFilename)
    {
    	$aLoggingDataOld = $this->_logger()->getLoggingData($sOldFilename, "d");
    	$oResult = rename($sOldFilename, $sNewFilename);

    	if($oResult) {
    		$this->_logger()->writeLog($aLoggingDataOld);
    		$this->_logger()->log($sNewFilename, "i");
    	}
    	return $oResult;
    }

    /**
     * write file (add logging)
     * override method at utilFile
     * @see utilFile::write()
     */
    public function write($sFilename, $sData)
    {
    	$sType = file_exists($sFilename) ? "u" : "i";  // set update mode if file is exist, else set insert mode
        $oResult = File::write($sFilename, $sData, FILE_MODE_WRITE);
    	if($oResult) $this->_logger()->log($sFilename, $sType);
    	return $oResult;
    }

	/**
     * append content to file (add logging)
     * @see utilFile::write()
     */
    public function append($sFilename, $sData)
    {
        $oResult = File::write($sFilename, $sData, FILE_MODE_APPEND);
    	if($oResult) $this->_logger()->log($sFilename, "u");
    	return $oResult;
    }

	/**
     * delete file (add logging)
     */
    public function delete($sFilename)
    {
    	$aLoggingData = $this->_logger()->getLoggingData($sFilename, "d");
    	$oResult = unlink($sFilename);
    	if($oResult) $this->_logger()->writeLog($aLoggingData);
    	return $oResult;
    }

    /**
     * mkdir (add logging)
     */
    public function makeDir($sDirName, $iMode , $bRecursive=null)
    {
        $oResult = @mkdir($sDirName, $iMode , $bRecursive);
        if($oResult) $this->_logger()->log($sDirName, "i");
        return $oResult;
    }

    /**
     * rmdir (add logging)
     */
    public function deleteDir($sDirName)
    {
        @rmdir($sDirName, $iMode);
        $this->_logger()->log($sDirName, "d");
        return true;
    }

	/**
     * File Logger Instance
     * @return utilFileLogger
     */
    protected function _logger()
    {
        return new utilFileLogger();
    }
}
