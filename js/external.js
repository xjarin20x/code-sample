$(document).ready(function(){
    
    /** Functions */
	function s2ab(s) {
		if(typeof ArrayBuffer !== 'undefined') {
			var buf = new ArrayBuffer(s.length);
			var view = new Uint8Array(buf);
			for (var i=0; i!=s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
			return buf;
		} 
		else {
			var buf = new Array(s.length);
			for (var i=0; i!=s.length; ++i) buf[i] = s.charCodeAt(i) & 0xFF;
			return buf;
		}
	}

	function exporter(id, type, fn, sn) {
		var fname = fn + '.' + type;
		switch (type) {
			case "xlsx":
			case "ods":
			case "csv":
			case "html":
				var wb = XLSX.utils.table_to_book(document.getElementById(id), {sheet: sn});
				var wbout = XLSX.write(wb, {bookType:type, bookSST:false, type: 'binary'});
				try {
					saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), fname);
				}
				catch(e) { 
					if(typeof console != 'undefined') console.log(e, wbout);
				}
				return wbout;
				break;
			case 'pdf':
				html2pdf(document.getElementById(id), {
				  margin:       0.5,
				  filename:     fname,
				  image:        { type: 'jpeg', quality: 1 },
				  html2canvas:  { dpi: 192, letterRendering: true },
				  jsPDF:        { unit: 'in', format: 'A4', orientation: 'portrait' }
				});
				break;
		}
	}

	function currentDate(){
		var d = new Date();
		var month = d.getMonth()+1;
		var day = d.getDate();

		var output = d.getFullYear() +
		    ((''+month).length<2 ? '0' : '') + month + 
		    ((''+day).length<2 ? '0' : '') + day;
		
		return output;
	}
    
    /** Pagination js */
    $('#resources-list').easyPaginate({
        paginateElement: 'div.media',
        elementsPerPage: 5,
        effect: 'default'
    });

    $('.easyPaginateNav a').on('click', function() {
        window.scrollTo(0, 0);
    });
    
    /** Load specific publications - vertical nav */
    $('body').on('click','a[publication]', function(e){
		e.preventDefault();
        $('.resource-nav a.active').removeClass('active');
        $(this).addClass('active');
		var c = $(this).attr("publication");
		var $loader = $('div.loading'), timer;
		$('html, body').animate({
		    scrollTop: ($('#primary-alt').offset().top)
		},0);
		jQuery.ajax({
			url: "retrieve.php",
			data:'pubcategory='+c,
			type: "POST",
			beforeSend: function(data) {
				timer && clearTimeout(timer);
			    timer = setTimeout(function(){
			   		$loader.show();
			    },
			    750);
			},
			success:function(data){
				clearTimeout(timer);
      			$loader.hide();
				$('div#resources-list').html(results = data);
                $('.easyPaginateNav').remove();
                $('#resources-list').easyPaginate({
                    paginateElement: 'div.media',
                    elementsPerPage: 5,
                    effect: 'default'
                });
			}
		});
	});
    
    /** Philippines map area highlight - hover */
    
    $("area").on("mouseover", function () {
        $(this).maphilight();
    });
    
    $('.map').maphilight({
        fill: false,
        fillOpacity: 0.1,
        stroke: true,
        strokeOpacity: 1,
        strokeColor: '003A05',
        strokeWidth: 1.5,
        groupBy: 'title'
    });
    
    /** Category selection - Survey Result */
    $('body').on('click','a[category]',function(e){
		e.preventDefault();
		$('html, body').animate({
		    scrollTop: 0
		},500);
		var c = $(this).attr("category");
		jQuery.ajax({
			url: "tables.php",
			data:'category='+c,
			type: "POST",
			success:function(data){$('#profile').html(data);}
		});
	});
    
    /** Multi-select JS */
    $('#table-input .multi-select').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonWidth: '400px',
        nonSelectedText: 'Not specified',
        filterPlaceholder: '',
        includeFilterClearBtn: false,
        onDropdownShown: function(e) {
            $trigger = $(this.$select).attr('id');
            $('#' + $trigger + '-options').show();
        },
        onDropdownHidden: function(e) {
            $trigger = $(this.$select).attr('id');
            $('#' + $trigger + '-options').hide();
        },
        onChange: function() {
            if($('#provinces').val() != "" && $('#seasons').val() != ""){
                $('#submit').attr('disabled', false);
            }
            else {
                $('#submit').attr('disabled', true);
            }
        }
    });
    
    $('#stat-input #data .multi-select').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonWidth: '400px',
        nonSelectedText: 'Not specified',
        filterPlaceholder: '',
        includeFilterClearBtn: false,
        onDropdownShown: function(e) {
            $trigger = $(this.$select).attr('id');
            $('#' + $trigger + '-options').show();
        },
        onDropdownHidden: function(e) {
            $trigger = $(this.$select).attr('id');
            $('#' + $trigger + '-options').hide();
        },
        onChange: function() {
            var isAllSelected = 1;
            $('.dynamic-select').each(function(i, obj) {
                if ($(this).val() == "") {
                    isAllSelected = 0;
                }
            });
            if( ($('#regions').val() != "" || $('#provinces').val() != "" || $('#cities').val() != "") && isAllSelected > 0 && $('#years').val() != ""){
                $('#process').attr('disabled', false);
            }
            else {
                $('#process').attr('disabled', true);
            }
        }
    });
    
    $('#stat-input #branch .multi-select').multiselect({
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        buttonWidth: '400px',
        nonSelectedText: 'Not specified',
        filterPlaceholder: '',
        enableClickableOptGroups: true,
        includeFilterClearBtn: false,
        onDropdownShown: function(e) {
            $trigger = $(this.$select).attr('id');
            $('#' + $trigger + '-options').show();
        },
        onDropdownHidden: function(e) {
            $trigger = $(this.$select).attr('id');
            $('#' + $trigger + '-options').hide();
        },
        onChange: function() {
            var isAllSelected = 1;
            $('.dynamic-select-st').each(function(i, obj) {
                if ($(this).val() == "") {
                    isAllSelected = 0;
                }
            });
            if($('#provinces-st').val() != "" && isAllSelected > 0 && $('#years-st').val() != ""){
                $('#process-st').attr('disabled', false);
            }
            else {
                $('#process-st').attr('disabled', true);
            }
        }
    });
    
    /** Ajax submit buttons */
    if($('div.loading').length) {
        $('div.loading').removeClass('d-flex');
    }

    $('body').on('submit', 'form#retrieve', function(e) {
		var $loader = $('div.loading'), timer;
        $.ajax({
            url     : $(this).attr('action'),
            type    : $(this).attr('method'),
            data    : $(this).serialize(),
            beforeSend: function(data) {
				timer && clearTimeout(timer);
			    timer = setTimeout(function(){
			    	$("#table-input").html("");
			   		$loader.addClass('d-flex');
			    },
			    750);
			}
        }).done(function(data) {
        	$("#table-input").html("");
        	clearTimeout(timer);
      		$loader.removeClass('d-flex');
            $('#table-output').html(data);
			fname = $('#table-title').html().match(/\b(\w)/g).join('').toUpperCase() + '_' + currentDate();
			sname = $('#table-title').html().match(/\b(\w)/g).join('').toUpperCase();
        });
        e.preventDefault();
    });
    
    $('body').on('submit', 'form#build', function(e) {
		var $loader = $('div.loading'), timer;
        $.ajax({
            url     : $(this).attr('action'),
            type    : $(this).attr('method'),
            data    : $(this).serialize(),
            beforeSend: function(data) {
				timer && clearTimeout(timer);
			    timer = setTimeout(function(){
			    	$("#stat-input").html("");
			   		$loader.addClass('d-flex');
			    },
			    750);
			}
        }).done(function(data) {
        	$("#stat-input").html("");
        	clearTimeout(timer);
      		$loader.removeClass('d-flex');
            $('#stat-output').html(data);
			fname = $('#stat-title').html().match(/\b(\w)/g).join('').toUpperCase() + '_' + currentDate();
			sname = $('#stat-title').html().match(/\b(\w)/g).join('').toUpperCase();
        });
        e.preventDefault();
    }); 
    
    $('body').on('click','#xlsx',function(e){
		e.preventDefault();
        return exporter('tableData','xlsx', fname, sname);
        //$("#feedback").modal('show', $(this));
	});

	$('body').on('click','#ods',function(e){
		e.preventDefault();
        return exporter('tableData','ods', fname, sname);
        //$("#feedback").modal('show', $(this));
	});

	$('body').on('click','#csv',function(e){
		e.preventDefault();
        return exporter('tableData','csv', fname, sname);
        //$("#feedback").modal('show', $(this));
	});

	$('body').on('click','#pdf',function(e){
		e.preventDefault();
        return exporter('tableData','pdf', fname, sname);
        //$("#feedback").modal('show', $(this));
	});

	$('body').on('click','#html',function(e){
		e.preventDefault();
        return exporter('tableData','html', fname, sname);
        //$("#feedback").modal('show', $(this));
	});

	$('body').on('click','#print',function(e){
        e.preventDefault();
        window.print(); 
        //$("#feedback").modal('show', $(this));
	});
    
    $('body').on('click','#send_cont',function(e){
        e.preventDefault();
        var r = $("#send_cont").attr('refer');
        var $loader = $('div.mini-loading'), timer;
        grecaptcha.ready(function() {
            grecaptcha.execute('6Lc65vUZAAAAAGnw7vUNyvMw3_VLSz5isXBVqW55', {action: 'submit_feedback'}).then(function(token) {
                $("#g-recaptcha-response").val(token);
                $.ajax({
                    url     : $("form#feedback_form").attr('action'),
                    type    : $("form#feedback_form").attr('method'),
                    data    : $("form#feedback_form").serialize(),
                    statusCode: {
                        500: function() {
                            clearTimeout(timer);
                            $loader.hide();
                            $('#feedback').modal('hide');
                        }
                    },
                    beforeSend: function(data) {
                        timer && clearTimeout(timer);
                        timer = setTimeout(function(){
                            $loader.show();
                        },
                        750);
                    }
                }).done(function(data) {
                    clearTimeout(timer);
      		        $loader.hide();
                    $('#feedback').modal('hide');
                }).fail(function(jqXHR) {
                    clearTimeout(timer);
      		        $loader.hide();
                    $('#feedback').modal('hide');
                });
            });
        });
        switch (r) {
            case "view-ref": window.open($("#" + r).attr('href'), '_blank'); break;
            case "dl-ref": {
                var anchor = document.createElement('a');
                anchor.href = $("#" + r).attr('href');
                anchor.download = $("#" + r).attr('download');
                document.body.appendChild(anchor);
                anchor.click();
            }
			case "xlsx": return exporter('tableData','xlsx', fname, sname); break;
			case "ods": return exporter('tableData','ods', fname, sname); break;
			case "csv": return exporter('tableData','csv', fname, sname); break;
			case "pdf": return exporter('tableData','pdf', fname, sname); break;
			case "html": return exporter('tableData','html', fname, sname); break;
			case "print": window.print(); break;
        }
	});
})