<?php

namespace JamesHeinrich\GetID3\Module\Audio;

/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
// See readme.txt for more details                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// module.audio.mod.php                                        //
// module for analyzing MOD Audio files                        //
//                                                            ///
/////////////////////////////////////////////////////////////////

class Mod extends \JamesHeinrich\GetID3\Module\Handler
{

	public function Analyze() {
		$info = &$this->getid3->info;
		$this->fseek($info['avdataoffset']);
		$fileheader = $this->fread(1088);
		if (preg_match('#^IMPM#', $fileheader)) {
			return $this->getITheaderFilepointer();
		} elseif (preg_match('#^Extended Module#', $fileheader)) {
			return $this->getXMheaderFilepointer();
		} elseif (preg_match('#^.{44}SCRM#', $fileheader)) {
			return $this->getS3MheaderFilepointer();
		} elseif (preg_match('#^.{1080}(M\\.K\\.|M!K!|FLT4|FLT8|[5-9]CHN|[1-3][0-9]CH)#', $fileheader)) {
			return $this->getMODheaderFilepointer();
		}
		$this->error('This is not a known type of MOD file');
		return false;
	}


	public function getMODheaderFilepointer() {
		$info = &$this->getid3->info;
		$this->fseek($info['avdataoffset'] + 1080);
		$FormatID = $this->fread(4);
		if (!preg_match('#^(M.K.|[5-9]CHN|[1-3][0-9]CH)$#', $FormatID)) {
			$this->error('This is not a known type of MOD file');
			return false;
		}

		$info['fileformat'] = 'mod';

		$this->error('MOD parsing not enabled in this version of getID3() ['.$this->getid3->version().']');
		return false;
	}

	public function getXMheaderFilepointer() {
		$info = &$this->getid3->info;
		$this->fseek($info['avdataoffset']);
		$FormatID = $this->fread(15);
		if (!preg_match('#^Extended Module$#', $FormatID)) {
			$this->error('This is not a known type of XM-MOD file');
			return false;
		}

		$info['fileformat'] = 'xm';

		$this->error('XM-MOD parsing not enabled in this version of getID3() ['.$this->getid3->version().']');
		return false;
	}

	public function getS3MheaderFilepointer() {
		$info = &$this->getid3->info;
		$this->fseek($info['avdataoffset'] + 44);
		$FormatID = $this->fread(4);
		if (!preg_match('#^SCRM$#', $FormatID)) {
			$this->error('This is not a ScreamTracker MOD file');
			return false;
		}

		$info['fileformat'] = 's3m';

		$this->error('ScreamTracker parsing not enabled in this version of getID3() ['.$this->getid3->version().']');
		return false;
	}

	public function getITheaderFilepointer() {
		$info = &$this->getid3->info;
		$this->fseek($info['avdataoffset']);
		$FormatID = $this->fread(4);
		if (!preg_match('#^IMPM$#', $FormatID)) {
			$this->error('This is not an ImpulseTracker MOD file');
			return false;
		}

		$info['fileformat'] = 'it';

		$this->error('ImpulseTracker parsing not enabled in this version of getID3() ['.$this->getid3->version().']');
		return false;
	}

}
