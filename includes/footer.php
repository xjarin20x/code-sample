<div class="clearfooter"></div>
<footer>
    <div id="main-footer" class="container-fluid">
        <div class="row mx-auto">
            <div id="col-1" class="col-md-6">
                <img src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/philrice_trademark.png" width="150" class="img-fluid float-left logo" alt="PhilRice logo">
                <img src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/socotec_logo.jpg" width="100" class="img-fluid float-right logo" alt="Socotec logo">
                <div class="clearfix"></div>
                <p id="project-description">PalayStat System is a core project of the Philippine Rice Research Institute under the supervision of the Socioeconomics Division.</p>
                <a href="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/rationale">Learn more</a>
            </div>
            <div id="col-2" class="col-md-6">
                <h6>Need data?</h6>
                <p>You can request data not available in PalayStat (subject to our data sharing policy).</p>
                <a class="btn bg-primary text-white text-uppercase rounded-pill" href="https://docs.google.com/forms/d/e/1FAIpQLSfd7JN7ZWxxV_tJz80osVbBKX9XYveur5RHs3hrRJZ2TFy6kQ/viewform" role="button">Request Data</a>
                <h6>We would like to hear from you!</h6>
                <p>Tell us your experience in using PalayStat.</p>
                <a class="btn bg-primary text-white text-uppercase rounded-pill" href="https://docs.google.com/forms/d/e/1FAIpQLSfvg9z9XwaAGuyxfXMHglmVrlCJbZ0rNBoekBgbRO_nHcN-mA/viewform" role="button">Give Feedback</a>
            </div>
        </div>
    </div>
</footer>
<?php
$ustmt = $conn->prepare("SELECT itemURL FROM cms_items WHERE (cms_items.itemCategory = 'Summary Tables' OR cms_items.itemCategory = 'Maps' OR cms_items.itemCategory = 'Publications' OR cms_items.itemCategory = 'Rice Statistics') AND cms_items.itemURL = ? LIMIT 1");
$ustmt->bind_param("s", $GLOBALS['base_URL']);
$ustmt->execute();
$ustmt->bind_result($db_url);
$ustmt->store_result();
$ustmt->fetch();
$ucount = $ustmt->num_rows;
if($ucount === 1 && ($db_url == $GLOBALS['base_URL'])){ ?>
<div class="modal fade" id="feedback" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="feedback-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="feedback_form" method="post" action="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/feedback.php">
            <div class="modal-header">
                <h3 class="modal-title" id="feedbackLabel">We would like to hear from you!</h3>
            </div>
            <div class="modal-body">
                <h4>Tell us your experience in using PalayStat.</h4>
                    <div class="form-group">
                        <label for="purpose-select">Purpose of acquired reference:</label>
                        <select id="purpose-select" class="form-control" name="purpose" tabindex="1" title="Not specified">
                            <option selected disabled>Purpose category</option>
                            <option value='1'>Academic Research</option>
                            <option value='2'>Policy-making</option>
                            <option value='3'>Scientific Research</option>
                            <option value='4'>Publications/Media</option>
                            <option value='5'>Proposal/Report Preparations</option>
                            <option value='6'>Marketing/Marketing Research</option>
                            <option value='7'>Machine Learning/Sample Dataset</option>
                            <option value='8'>Others</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rate-use">Rate your experience in PalayStat.</label>
                        <input id="rate-use" class="likert" name="rate-use" type="number" value="4">
                        <input id="acc-use" name="accuse" value="">
                    </div>
                    <div class="form-group">
                        <input type="hidden" id="userID" name="userID" value="<?php echo substr(sha1(time()), 0, 16); ?>">
                        <input type="hidden" id="source" name="source" value="<?php echo $purifier->purify($db_url); ?>">  
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
                    </div>
                    <br/>
                    <p class="small">Protected by reCAPTCHA. Google <a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a> and <a href="https://policies.google.com/terms" target="_blank">TOS</a> apply.</p>
          </div>
          <div class="modal-footer">
            <div class="mini-loading">
                <div class="spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                </div>
            </div>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>  
            <input id="send_cont" class="btn btn-primary" refer="none" disabled="disabled" type="submit" value="Send and continue">
          </div>
     </form>
    </div>
  </div>
</div>
<?php $ustmt->close(); } ?> 
<div id="gwt-standard-footer"></div>
<script type="text/javascript">
(function(d, s, id) {
	var js, gjs = d.getElementById('gwt-standard-footer');
	js = d.createElement(s); js.id = id;
	js.src = "//gwhs.i.gov.ph/gwt-footer/footer.js";
	gjs.parentNode.insertBefore(js, gjs);
}(document, 'script', 'gwt-footer-jsdk'));
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/jquery.maphilight.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/fitvids.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/paginate.js"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/xlsx.full.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/FileSaver.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/html2pdf.bundle.min.js"></script>

<script type="text/javascript" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/js/external.js"></script>
</body>
</html>