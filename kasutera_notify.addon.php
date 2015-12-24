<?php

Context::addHtmlfooter('<script>
;(function($){
$.ajaxSetup({
global: false
});
})(jQuery);
</script>');
Context::addCSSFile("./addons/kasutera_notify/css/notify.css", false);
	/*
	kasutera_notify.php
	새글이 등록되어있을 경우를 체크 후 알림
	*/
	if($called_position == 'before_module_proc') {
		$is_document_new = false;
		if(!$addon_info->settime){
			$time_interval = 60; //글 체크 주기 초단위
			$settimeout = 60000;
		}else{
			$time_interval = $addon_info->settime;
			$settimeout = $time_interval*1000;
		}
		if(!$addon_info->hidetime) $hide_time = 3000;
		else $hide_time = $addon_info->hidetime * 1000;
		if(!$addon_info->ajaxtarget) $ajax_target = '';
		else $ajax_target = '?mid='.$addon_info->ajaxtarget;
		$time_check = date("YmdHis", time()-$time_interval);
		$args->list_count = 1;
		$args->order_type = 'asc';
		$args->statusList = 'PUBLIC';
		if(isset($addon_info->exclude_module_srl)) $args->exclude_module_srl = $addon_info->exclude_module_srl;
		$args->module_srl = $addon_info->module_srl;
		$output = executeQueryArray('document.getDocumentList', $args);
		if(!count($output->data)) return;
		if($output->data)
		{
			foreach($output->data as $doc)
			{
				if($doc->regdate > $time_check)	$is_document_new = true; //현재 시간으로 부터 1분안에 등록된 글이 있을경우
			}
		}

			if($is_document_new == true && $_COOKIE['docsrl']!=$doc->document_srl){
				$addBody = '<script type="text/javascript">document.cookie = "docsrl='.$doc->document_srl.'";</script>';
				$addLayerdiv ='<div class="notify_body"><div class="notify_badge"><div class="badge_body" style="cursor: pointer;" onclick="location.href='.$doc->document_srl.'"><p class="badge_title"><b>새 글 알림</b></p><p class="badge_article">'.$doc->title.'</p></div><div class="badge_close"><a href="javascript:ndcClose();"><i class="xi-close-circle"></i></a></div></div></div>';
			}
			else{
				$addBody = '';
				$addLayerdiv ='';
			}
			$nJquery = '<script type="text/javascript">function newdocumentchk() {jQuery(".notify_body").load(request_uri+"index.php'.$ajax_target.' .notify_badge");setTimeout(newdocumentchk, '.$settimeout.');}function ndcClose() {jQuery(".notify_badge").fadeOut("slow");}setTimeout(newdocumentchk, '.$settimeout.');setTimeout(function(){jQuery(".nodify_badge").fadeOut("slow");}, '.$hide_time.');</script>';
			Context::addBodyHeader($addLayerdiv);
			Context::addBodyHeader($nJquery);
			Context::addBodyHeader($addBody);
	}

?>
