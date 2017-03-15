<link rel="stylesheet" property="stylesheet"  href="<?=EdFaq::$url?>css/faqPage.css" type="text/css" />
<script>

function MM_jumpMenu(targ,selObj,restore){ //v3.0

	eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  	if (restore) selObj.selectedIndex=0;
}

</script>
<div>
<!--a name="top"></a-->
<select name="menu1" class="content" title="Select Question" onchange="MM_jumpMenu('document',this,0)">
<? $j=1;
forEach ($faqs as $faqIndex=>$ans): ?>
<option value="#<?=$j?>"><?=$ans->ques?></option>

<? $j++;
 endForEach ?>

</select>
</div>

<ol style="margin-top:20px;">
     <? 
	$i=1;
	forEach ($faqs as $faqIndex=>$faq): ?>
	<!--a name="<?=$i?>"></a-->
        <li>
	    <div class="edfaq_ques" id="<?=$i?>"><?=stripslashes($faq->ques)?></div>
            <div class="edfaq_ans"><?=stripslashes($faq->answr)?></div>
	    <a href="#" class="edfaq_bktop" onclick="window.scrollTo(0,0); return false">Back to Top</a>
        </li>
	
		
	
	
    <? $i++;
endForEach ?>
</ol>
