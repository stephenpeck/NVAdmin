{include file=$SESSION.ENVIRONMENT.EN_HEADER}


 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  
  
  <h2>{$SCRAPERLOG.SL_DATETIME} Page {$SCRAPERLOG.SL_PAGENAME} Result {$SCRAPERLOG.SL_STATUS}</h2>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>

 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  
 <pre>  {$SCRAPERLOG.SL_REQUEST} </pre>
  
  
  </div>
  <div class="col-sm-1"></div>
</div>

 <div class="row">
  <div class="col-sm-1"></div>
  <div class="col-sm-10">
  
  
   {$SCRAPERLOG.SL_RESPONSE} >
  
  
  </div>
  <div class="col-sm-1"></div>
</div>


{include file=$SESSION.ENVIRONMENT.EN_FOOTER}