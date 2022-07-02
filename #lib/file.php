<?php

/**
 * 檔案相關
 */
class file
{
	/**
	 * 副檔名
	 */
	public static $extentFileName = array(
		'img' => array('jpg', 'jpeg', 'bmp', 'png', 'webp', 'tif', 'tiff'),
		'video' => array('.webm', '.mkv', '.flv', '.flv', '.vob', '.ogv', '.ogg', '.drc', '.gif', '.gifv', '.mng', '.avi', '.mts', '.m2ts', '.ts', '.mov', '.qt', '.wmv', '.yuv', '.rm', '.rmvb', '.viv', '.asf', '.amv', '.mp4', '.m4p', '.m4v', '.mpg', '.mp2', '.mpeg', '.mpe', '.mpv', '.mpg', '.mpeg', '.m2v', '.m4v', '.svi', '.3gp', '.3g2', '.mxf', '.roq', '.nsv', '.flv', '.f4v', '.f4p', '.f4a', '.f4b'),
		'audio' => array('.3gp', '.aa', '.aac', '.aax', '.act', '.aiff', '.alac', '.amr', '.ape', '.au', '.awb', '.dct', '.dss', '.dvf', '.flac', '.gsm', '.iklax', '.ivs', '.m4a', '.m4b', '.m4p', '.mmf', '.mp3', '.mpc', '.msv', '.nmf', '.ogg', '.oga', '.mogg', '.opus', '.ra', '.rm', '.raw', '.rf64', '.sln', '.tta', '.voc', '.vox', '.wav', '.wma', '.wv', '.webm', '.8svx', '.cda'),
		'doc' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'ods', 'odt', 'odp', 'odg', 'odb', 'pdf'),
	);

	/**
	 * 檔案上傳
	 * @param string $name 上傳欄位名
	 * @param string $path 路徑
	 * @param array $option 選項
	 *  - size 大小限制
	 *  -- 0 不限 (default)
	 *  - name 檔名命名方式
	 *  -- md5 md5_file (default)
	 *  -- radom 隨機
	 *  - exist 存在處理方式
	 *  -- pass 略過 (default)
	 *  -- rename 重新產生
	 *  -- ext 附加流水號
	 *  -- cover 覆蓋
	 *  - role 檔案限制
	 *  -- (empty) 不限 (default)
	 *  -- img 圖片檔
	 *  -- video 影片檔
	 *  -- audio 聲音檔
	 *  -- media 媒體檔(圖片+影片)
	 *  -- doc 文件檔
	 */
	public static function upload(string $name, string $path, array $option = array()): array
	{
		$filename = array();

		if (!isset($option['size']) || !$option['size']) {
			$option['size'] = 0;
		}

		if (!isset($option['name']) || !$option['name']) {
			$option['name'] = 'md5';
		}

		if (!isset($option['exist']) || !$option['exist']) {
			$option['exist'] = 'pass';
		}

		if (!isset($option['role']) || !$option['role']) {
			$option['role'] = '';
		}

		if (!$_FILES) {
			return $filename;
		}

		// 單檔
		if (isset($_FILES[$name]['error'])) {
			$_FILES[$name] = array($_FILES[$name]);
		}

		foreach ($_FILES[$name] as $row) {
			$tmp = '';
			$exce = true;
			if ($row['error'] || !is_uploaded_file($row['tmp_name'])) {
				continue;
			}

			if ($option['size'] && $row['size'] > $option['size']) {
				continue;
			}

			$extentFileName = mb_substr($row['name'], mb_strrpos($row['name'], '.'));

			if ($option['role'] && isset(self::$extentFileName[$option['role']]) && !in_array($extentFileName, self::$extentFileName[$option['role']])) {
				continue;
			}

			switch ($option['name']) {
				case 'radom':
					$id = tool::radomString(26, 'full');
					break;

				case 'md5':
				default:
					$id = md5_file($row['tmp_name']);
					break;
			}

			if (file_exists($path . $id . $extentFileName)) {
				switch ($option['exist']) {
					case 'rename':
						do {
							$id = tool::radomString(26, 'full');
						} while (file_exists($path . $id . $extentFileName));
						break;

					case 'ext':
						$i = 0;
						do {
							$tmpID = $id . '_' . (++$i);
						} while (file_exists($path . $tmpID . $extentFileName));
						$id = $tmpID;
						break;

					case 'cover':
						unlink($path . $id . $extentFileName);
						break;

					case 'pass':
					default:
						$exce = false;
						break;
				}
			}

			if ($exce && move_uploaded_file($row['tmp_name'], $path . $id . $extentFileName)) {
				$tmp = $id . $extentFileName;
			}

			$filename[] = $tmp;
		}

		return $filename;
	}

	/**
	 * 檔案下載
	 * @param string $path 資料夾路徑
	 * @param string $targetName 目標檔案名稱
	 * @param string $fileName 下載檔案名稱
	 */
	public static function download(string $path, string $targetName, string $fileName = '')
	{
		$realPath = '';
		$targetName = mb_ereg_replace('^.*[\/\\\\]', '', $targetName);
		$fileName = mb_ereg_replace('^.*[\/\\\\]', '', $fileName);
		if (!$fileName) {
			$fileName = $targetName;
		}

		$realPath = realpath($path . '/' . $targetName);

		if ($realPath) {
			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: Binary');
			header('Content-disposition: attachment; filename="' . ($fileName ?: $targetName) . '"');
			readfile($realPath);
		} else {
			header("HTTP/1.0 404 Not Found");
		}
	}

	/**
	 * 檔案瀏覽
	 * @param string $path 資料夾路徑
	 * @param string $targetName 目標檔案名稱
	 */
	public static function brower(string $path, string $fileName, $option =  array()){
		$fileName = mb_ereg_replace('([\\\/]\.{1,2}[\\\/])', '/', $fileName);

		$realPath = realpath($path . '/' . $fileName);

		if($realPath){
			header('Content-Type: ' . (isset($option['contentType']) ? $option['contentType'] : finfo_file(finfo_open(FILEINFO_MIME_TYPE), $realPath)));
			readfile($realPath);
		} else {
			header("HTTP/1.0 404 Not Found");
		}
	}
}
