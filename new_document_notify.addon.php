<?php

Context::addHtmlfooter('<script>
;(function($){
$.ajaxSetup({
global: false
});
})(jQuery);
</script>');

	/*
	new_document_notify.addon.php
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
			if(!$addon_info->lineborder) $addon_info->lineborder = '1px';
			if(!$addon_info->bgcolor) $addon_info->bgcolor = '#EBEEF4';
			if(!$addon_info->linecolor) $addon_info->linecolor = '#369';
			if(!$addon_info->fontcolor) $addon_info->fontcolor = '#369';
			if(!$addon_info->cuttitle) $doc->title = cut_str($doc->title,$addon_info->cuttitle,'...');

			if($is_document_new == true && $_COOKIE['docsrl']!=$doc->document_srl){
				$addBody = '<script type="text/javascript">document.cookie = "docsrl='.$doc->document_srl.'";</script>';
				$addLayerdiv ='<div id="ndc"><div id="ndcLayer" style="position:fixed;display:block;left:100%;top:100%;margin-top:-83px;margin-left:-365px;width:350px;background:#FFF;border:'.$addon_info->lineborder.' solid '.$addon_info->linecolor.';z-index:'.$addon_info->notifyzindex.';color:'.$addon_info->fontcolor.';"><div style="border:1px solid #FFF;background: '.$addon_info->bgcolor.';"><span style="display:inline-block;width:280px;font:11px Dotum;letter-spacing:-1px;line-height: 22px;padding: 4px 10px;margin: 0 10px 0 0;height: 20px;text-shadow:1px 1px 0 #FFF">[알림]&nbsp;&nbsp;<b>'.$doc->nick_name.'</b>님이 새글을 등록하셧습니다.</span><span><a href="javascript:ndcClose();" style="text-decoration:none;text-shadow:1px 1px 0 #FFF;font:11px verdana;letter-spacing:-1px;color:'.$addon_info->fontcolor.'">Close</a></span></div><div class="alram" style="border-top:1px solid '.$addon_info->linecolor.';background:#FFF;padding: 5px 10px"><a style="font:700 12px Gulim;letter-spacing:-1px;height: 28px;line-height: 30px;display:block;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;-o-text-overow: ellipsis;-moz-binding:url(js/ellipsis.xml#ellipsis)undefinedundefinedundefined;color:'.$addon_info->fontcolor.'" href='.$doc->document_srl.'>'.$doc->title.'</a></div></div></div>';
			}
			else{
				$addLayerdiv ='<div id="ndc"><div id="ndcLayer" style="position:fixed;display:none;left:100%;top:100%;margin-top:-50px;margin-left:-330px;width:300px;height:30px;padding:5px;font-size:11px;background:'.$addon_info->bgcolor.';border:'.$addon_info->lineborder.' solid '.$addon_info->linecolor.';z-index:'.$addon_info->notifyzindex.'"><span></span></div></div>';
				$addBody = '';
			}
			$nJquery = '<script type="text/javascript">function newdocumentchk() {jQuery("#ndc").load(request_uri+"index.php'.$ajax_target.' #ndcLayer");setTimeout(newdocumentchk, '.$settimeout.');}function ndcClose() {jQuery("#ndcLayer").fadeOut("slow");}setTimeout(newdocumentchk, '.$settimeout.');setTimeout(function(){jQuery("#ndcLayer").fadeOut("slow");}, '.$hide_time.');</script>';
			
			Context::addBodyHeader($addLayerdiv);
			Context::addBodyHeader($nJquery);
			Context::addBodyHeader($addBody);

			
			
	}

?>
