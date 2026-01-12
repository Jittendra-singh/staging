$(document).ready(function () {
      /**element initialization */
      $.ajaxSetup({
            beforeSend: function () {
                  // $('.ajax-loader').show();
                  //One.loader('show')
            },
            complete: function () {
                  // $('.ajax-loader').hide();
                  //One.loader('hide')
            },
            headers: {
                  'Accept': 'application/json',
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                  422: function(responseObject, textStatus, jqXHR) {
                        let responseText = JSON.parse(responseObject.responseText);
                        showError(responseText.message , 'error');
                  },
                  405: function(responseObject, textStatus, jqXHR) {
                        let responseText = JSON.parse(responseObject.responseText);
                        showError(responseText.message , 'error');
                  }
            }
      });
      $(document).on('click', '.delete-record', function () {
            //$('.delete-record').click(function(){
            let context = $(this);
            Swal.fire({
                  title: "Are you sure?",
                  text: `You want to delete this record?`,
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
            }).then((willDelete) => {
                  if (willDelete.isConfirmed) {
                        $.ajax({
                              url: `${base}/${$(context).data('module')}/${$(context).data('id')}`,
                              data: '',
                              method: 'DELETE',
                              success: function (res) {
                                    if (res.status) {
                                          $(context).parents('tr').first().remove();
                                          showError(res.message, 'success')
                                    } else {
                                          showError(res.message, 'error')
                                    }
                                    return;
                              },
                        });
                  }
            });
      })
      $('.update-user-credentials').click(function () {
            let context = $(this);
            $('#pass_user_id').val(context.data('id'));
      })
      $('.nav-main-link-submenu').click(function () {
            $(this).parents('li').first().toggleClass('open');
      });
      $('#page-header-user-dropdown').click(function () {
            $('#page-header-user-dropdown').toggleClass('show');
            $('#page-header-user-dropdown').next('.dropdown-menu').toggleClass('show');
      });
      /* if ($('#custom_datatable').length) {
            var oTable = $('#custom_datatable').DataTable();
            if ($('#custom_datatable').attr('sort')) {
                  oTable.order([$('#custom_datatable').attr('sort'), 'desc']).draw();
            }
      } */
      if ($('.select2').length) {
            $('.select2').select2();

            if ($('#bring_tools').length && $('#provided_tools').length) {
                  $('#bring_tools').on('select2:select', function (e) {
                        let selectedId = e.params.data.id;
                        let $providedTools = $('#provided_tools');
                        let option = $providedTools.find(`option[value="${selectedId}"]`);
                        option.prop('disabled', true);
                        $providedTools.trigger('change.select2');
                  });
                  $('#bring_tools').on('select2:unselect', function (e) {
                        let unselectedId = e.params.data.id;
                        let $providedTools = $('#provided_tools');
                        let option = $providedTools.find(`option[value="${unselectedId}"]`);
                        option.prop('disabled', false);
                        $providedTools.trigger('change.select2');
                  });

                  $('#provided_tools').on('select2:select', function (e) {
                        let selectedId = e.params.data.id;
                        let $bring_tools = $('#bring_tools');
                        let option = $bring_tools.find(`option[value="${selectedId}"]`);
                        option.prop('disabled', true);
                        $bring_tools.trigger('change.select2');
                  });
                  $('#provided_tools').on('select2:unselect', function (e) {
                        let unselectedId = e.params.data.id;
                        let $bring_tools = $('#bring_tools');
                        let option = $bring_tools.find(`option[value="${unselectedId}"]`);
                        option.prop('disabled', false);
                        $bring_tools.trigger('change.select2');
                  });
            }
      }
      if ($('#select2product').length) {
            $('#select2product').select2({
                  templateResult: productFormatState
            });
            function productFormatState(state) {
                  if (!state.id) {
                        return state.text;
                  }
                  console.log(state);
                  console.log($(state.element).attr('img'));
                  var baseUrl = "/user/pages/images/flags";
                  var $state = $(
                        '<span><img width="90" src="' + $(state.element).attr('img') + '" class="img-flag" /> ' + state.text + '</span>'
                  );
                  return $state;
            };
      }
      if ($('#select2productinquiry').length) {
            $('#select2productinquiry').select2({
                  templateResult: productFormatState
            });
            function productFormatState(state) {
                  if (!state.id) {
                        return state.text;
                  }
                  console.log(state);
                  console.log($(state.element).attr('img'));
                  var baseUrl = "/user/pages/images/flags";
                  var $state = $(
                        '<span><img width="90" src="' + $(state.element).attr('img') + '" class="img-flag" /> ' + state.text + '</span>'
                  );
                  return $state;
            };
      }
      if ($('.select2ajax').length) {
            $('.select2ajax').each(function () {
                  var datatype = $(this).attr('datatype');
                  var url = '';
                  if(datatype=='organiser'){
                        url = base + '/get-organizer-options';
                  } else if(datatype=='event'){
                        url = base + '/get-event-options';
                  } else if(datatype=='volunteer'){
                        url = base + '/get-volunteer-options';
                  } else if(datatype=='all_volunteers'){
                        url = base + '/get-all-volunteer-options';
                  } else if(datatype=='pincode'){
                        url = base + '/get-pincode-options';
                  }
                  $(this).select2({
                        placeholder: 'Search for option',
                        minimumInputLength: 1,
                        ajax: {
                              url: url,
                              method: 'POST',
                              data: function (params) {
                                    var query = {
                                          search: params.term,
                                          type: 'public'
                                    }
                                    // Query parameters will be ?search=[term]&type=public
                                    return query;
                              }
                        }
                  });
            });
      }
      if ($('.datepicker').length) {
            $('.datepicker').datepicker();
      }
      // let myDropzone = $(".file-upload").Dropzone();
      let showAlert = show_alert || '';
      showError(showAlert.message, showAlert.class);
      $('#list-view-btn').click(function () {
            $('#grid-view').hide();
            $('#list-view').show();
      });
      $('#grid-view-btn').click(function () {
            $('#grid-view').show();
            $('#list-view').hide();
      });
      if ($('.validateform').length) {
            $('.validateform').validate({
                  rules: {
                        '.required': {
                              required: true,
                        }
                  },
                  highlight: function (element, errorClass, validClass) {
                        $(element).css('border-color', 'red');
                  },
                  unhighlight: function (element, errorClass, validClass) {
                        $(element).css('border-color', '#dfe3ea');
                  }
            });
      }
      $('.triggger-role').trigger('change');
      $(document).on('change', '.statusswitch', function () {
            $(this).attr('disabled', 'disabled');
            var url = $(this).attr('url');
            window.location.href = url;
      });
      if ($('.only_date_picker').length) {
            $('.only_date_picker').datetimepicker({
                  format:'Y-m-d',
                  timepicker:false,
            });
      }
	if ($('.only_datetime_picker').length) {
		$('.only_datetime_picker').datetimepicker({
			format:'Y-m-d H:i',
			step:30,
		});
	}
	//if($('#start_datetime').length && $('#site_publish_date').length && $('#ticket_sell_start_time').length){
	if($('#start_datetime').length && $('#site_publish_date').length){
		$('#start_datetime').datetimepicker({
			format:'Y-m-d H:i',
			step:30,
			minDate:0,
			onShow:function( ct ){
				this.setOptions({
					//maxDate:jQuery('#end_datetime').val()?jQuery('#end_datetime').val():false
				})
			},
                  /* onChangeDateTime:function( ct ){
                        if(ct){
                              var dateEnd2 = new Date(ct);
                              dateEnd2.setDate(dateEnd2.getDate() - 1);
                              var month = (dateEnd2.getMonth() + 1).toString().padStart(2, '0');
                              var day = dateEnd2.getDate().toString().padStart(2, '0');
                              var year = dateEnd2.getFullYear();
                              var formattedDate = year + '-' + month + '-' + day;
                              $('#ticket_sell_end_time').val(formattedDate);
                              
                              var dateEnd = new Date(ct);
                              dateEnd.setDate(dateEnd.getDate() - 2);
                              var month = (dateEnd.getMonth() + 1).toString().padStart(2, '0');
                              var day = dateEnd.getDate().toString().padStart(2, '0');
                              var year = dateEnd.getFullYear();
                              var formattedDate = year + '-' + month + '-' + day;
                              $('#ticket_sell_start_time').val(formattedDate);
                        }
                  } */
		});
		$('#end_datetime').datetimepicker({
			format:'Y-m-d H:i',
			step:30,
			onShow:function( ct ){
				this.setOptions({
					minDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():0
				})
			},
                  onChangeDateTime:function( ct ){
                        if(ct){
                              var dateEnd = new Date(ct);
                              dateEnd.setDate(dateEnd.getDate() + 1);
                              var month = (dateEnd.getMonth() + 1).toString().padStart(2, '0');
                              var day = dateEnd.getDate().toString().padStart(2, '0');
                              var year = dateEnd.getFullYear();
                              var formattedDate = year + '-' + month + '-' + day;
                              $('#site_depublish_date').val(formattedDate);
                        }
                  }
		});
		$('#site_publish_date').datetimepicker({
			scrollMonth: false,
                  scrollInput: false,
			format:'Y-m-d',
  			timepicker:false,	
			onShow:function( ct ){
				this.setOptions({
					maxDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():false
				})
			},
			onChangeDateTime: function (ct) {
				var dateEnd = new Date(ct);
				var month = (dateEnd.getMonth() + 1).toString().padStart(2, '0');
				var day = dateEnd.getDate().toString().padStart(2, '0');
				var year = dateEnd.getFullYear();
				var formattedDate = year + '-' + month + '-' + day;
				$('.tk_sell_start_date').val(formattedDate);
			}
		});
		$('#site_depublish_date').datetimepicker({
			scrollMonth: false,
                  scrollInput: false,
			format:'Y-m-d',
  			timepicker:false,	
			onShow:function( ct ){
				this.setOptions({
					minDate:jQuery('#end_datetime').val()?jQuery('#end_datetime').val():false
				})
			},
		});
		callSellStartEndDatePicker();
		/* $('#ticket_sell_start_time').datetimepicker({
			scrollMonth: false,
                  scrollInput: false,
			format:'Y-m-d',
  			timepicker:false,	
			onShow:function( ct ){
                        var maxDateForSellDate = false;
                        if(jQuery('#start_datetime').val()!=''){
                              var dateEnd = new Date(jQuery('#start_datetime').val());
                              dateEnd.setDate(dateEnd.getDate() - 1);
                              maxDateForSellDate = dateEnd.getFullYear()+'-'+(dateEnd.getMonth()+1)+'-'+dateEnd.getDate();
                        }
				this.setOptions({
					//maxDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():false
					maxDate:maxDateForSellDate?maxDateForSellDate:false
				})
			},
		});
		$('#ticket_sell_end_time').datetimepicker({
			scrollMonth: false,
                  scrollInput: false,
			format:'Y-m-d',
  			timepicker:false,	
			onShow:function( ct ){
                        var maxDateForSellDate = false;
                        if(jQuery('#start_datetime').val()!=''){
                              var dateEnd = new Date(jQuery('#start_datetime').val());
                              dateEnd.setDate(dateEnd.getDate() - 1);
                              maxDateForSellDate = dateEnd.getFullYear()+'-'+(dateEnd.getMonth()+1)+'-'+dateEnd.getDate();
                        }
				this.setOptions({
					maxDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():false,
					//maxDate:maxDateForSellDate?maxDateForSellDate:false,
                              minDate:jQuery('#ticket_sell_start_time').val()?jQuery('#ticket_sell_start_time').val():false
				})
			},
		}); */
	}
      $('[data-bs-dismiss="modal"]').click(function () {
            $('.modal').modal('hide');
      });
      if ($('#email').length) {
            callImaskForEmail('email');
            $('#email').blur(function () {
                  callImaskForEmail('email');
            });
      }
      $(document).on('click', '.seemore', function () {
            var id = $(this).attr('showhideid');
            $(this).hide();
            console.log("dfdsfsdfsdf");
            console.log(id);
            console.log($('.seelessdiv' + id));
            $('.seelessdiv' + id).show();
            // $('.seeless' + id).show();
      });
      $(document).on('click', '.seeless', function () {
            var id = $(this).attr('showhideid');
            $('.seelessdiv' + id).hide();
            $('.seemore' + id).show();
      });
      $('.detailed-modal-popup').on('shown.bs.modal', function (event) {
            
            let target_button = event.relatedTarget;
            let modelId = event.target.id;
            let detailsDiv = $(target_button).data('details') || 'tr-detail';
            let Jsondata = $(target_button).parents('tr').first().find(`.${detailsDiv}`).html();
            console.log(Jsondata);
            let data = JSON.parse(Jsondata);
            populateData(modelId, data)
      })
      $('.forgot-password-modal').on('shown.bs.modal', function (event) {
            let target_button = event.relatedTarget;
            let route = $(target_button).data('action');
            // alert(route);
            $('.forgot-password-modal form').attr('action' , route);
      })
	$('.generate-event-meta-data').on('click',async function(event){
		$('#focus_keywords, #meta_title, #meta_description').val('').attr('disabled','disabled');
		let target = $('.generate-event-meta-data');
		$('.bx-loader').addClass('bx-spin');
		target.text('Generating...');
		await $.ajax({
				type: "POST",
				url: $(target).data('url'),
				data: {
                              'event_name': $('#event_name').val(),
                              'description': $('#description').val(),
                              'start_datetime': $('#start_datetime').val(),
                              'end_datetime': $('#end_datetime').val(),
                              'venue': $('#venue').val(),
                              'pincode': $('#pincode').val()
                        },
				success: function (response) {
					target.text('Generate By AI');
					if (response.status) {
						$('#meta_title').val(response.data.meta_title).removeAttr('disabled');
						$('#meta_description').val(response.data.meta_description).removeAttr('disabled');
						$('#focus_keywords').val(response.data.meta_keywords).removeAttr('disabled');
					}
					else {
							showError(response.message, 'error');
					}
				},
		});
		$('.bx-loader').removeClass('bx-spin');
	})
      $('.generate-focus-keywords').on('click',async function(event){
            let target = $('.generate-focus-keywords');
            console.log($(target).data('url'));
            $('.bx-loader').addClass('bx-spin');
            await $.ajax({
                  type: "POST",
                  url: $(target).data('url'),
                  data: {
                        'meta_title': $('#meta_title').val(),
                        'meta_description': $('#meta_description').val(),
                  },
                  success: function (response) {
                        if (response.status) {
                              $('#focus_keywords').val(response.data.keywords);
                        }
                        else {
                              showError(response.message, 'error');
                        }
                  },
            });
            $('.bx-loader').removeClass('bx-spin');
      })
      $('.add-ticket').on('click', function (event) {
            let target        = $(event.target);
            let route         = $(target).data('action');
            let clonedElement = $('.ticket-form-replica .ticket-form').clone();
            let nextIndex     = $('.ticket-container .ticket-form').last().length ? $('.ticket-container .ticket-form').last().find('.current_counter').val() || -1 : -1;
            nextIndex         = parseInt(nextIndex) + 1;
            $(clonedElement).find('.current_counter').val(nextIndex);
            $(clonedElement).find('.form-ele').each(function() {
                  let name    = $(this).attr('name');
                  name        = name.replace('counter_placeholder' , nextIndex);
                  $(this).attr('name', name);
            });

		$(clonedElement).find('[data-bs-target="#collapse_counter_placeholder"]').attr('aria-controls', `collapse_${nextIndex}`);
		$(clonedElement).find('[data-bs-target="#collapse_counter_placeholder"]').attr('data-bs-target', `#collapse_${nextIndex}`);
		$(clonedElement).find('#collapse_counter_placeholder').attr('id', `collapse_${nextIndex}`);
		$(clonedElement).find('#collapse_title_counter_placeholder').attr('id', `collapse_title_${nextIndex}`);
		$(clonedElement).find(`#collapse_title_${nextIndex}`).text(nextIndex+1);

            $(clonedElement).appendTo('.ticket-container');
            fieldmasking();
		$('.only_date_picker').datetimepicker({
			format: 'Y-m-d',
			timepicker: false,
		});
		callSellStartEndDatePicker();
      })
	$(document).on('change', '.ticket_type_select', function () {
		let name = $(this).attr('name');
		let counter = name.split('[')[1].split(']')[0];
		let selectedValue = $(this).val();
		let ticketPriceInput = $('[name="ticket[' + counter + '][ticket_price]"]');
		if (selectedValue === 'free') {
			ticketPriceInput.val(0);
			ticketPriceInput.prop('readonly', true);
		} else {
			ticketPriceInput.prop('readonly', false);
		}
	});
      $(document).on('click', '.delete-ticket', function (event) {
            $(this).parents('.ticket-form').first().remove();
            // Swal.fire({
            //       title: "Are you sure?",
            //       text: `You want to delete this record?`,
            //       icon: "warning",
            //       showCancelButton: true,
            //       confirmButtonColor: '#3085d6',
            //       cancelButtonColor: '#d33',
            //       confirmButtonText: 'Yes, delete it!'
            // }).then((willDelete) => {
            //       if (willDelete.isConfirmed) {
            //       }
            // });
      });
      if($('#organisation_short_description').length){
            /* document.querySelectorAll('#organisation_short_description').forEach(editor => {
                  ClassicEditor
                        .create( editor, {
                              toolbar: [
                              'heading',
                              'bold', 'italic', 'underline',
                              'bulletedList', 'numberedList',
                              'link',
                              'undo', 'redo'
                              ]
                        } )
                        .then( function(editor) {
                        editor.ui.view.editable.element.style.height = '50px';   
                        })
                        .catch( function(error) {
                        console.error( error );
                        });
            }); */
            let editor = document.getElementById('organisation_short_description');
            ClassicEditor
                        .create( editor, {
                              toolbar: [
                              'heading',
                              'bold', 'italic', 'underline',
                              'bulletedList', 'numberedList',
                              'link',
                              'undo', 'redo'
                              ]
                        } )
                        .then( function(editor) {
                        editor.ui.view.editable.element.style.height = '50px';   
                        })
                        .catch( function(error) {
                        console.error( error );
                        });
      }
      $(document).on('change', '[name="is_organiser"]', function (event) {
            // onchange="$(this).val() == 1 ? $('.organisation-details').show() : $('.organisation-details').hide()"
            if($(this).val() == 1){
                  $('.organisation-details').show()
                  /* document.querySelectorAll('#organisation_short_description').forEach(editor => {
                        ClassicEditor
                              .create( editor, {
                                    toolbar: [
                                    'heading',
                                    'bold', 'italic', 'underline',
                                    'bulletedList', 'numberedList',
                                    'link',
                                    'undo', 'redo'
                                    ]
                              } )
                              .then( function(editor) {
                              editor.ui.view.editable.element.style.height = '50px';   
                              })
                              .catch( function(error) {
                              console.error( error );
                              });
                  }); */
                  
                  /* let editor = document.getElementById('organisation_short_description');
                  ClassicEditor
                        .create( editor, {
                              toolbar: [
                              'heading',
                              'bold', 'italic', 'underline',
                              'bulletedList', 'numberedList',
                              'link',
                              'undo', 'redo'
                              ]
                        } )
                        .then( function(editor) {
                        editor.ui.view.editable.element.style.height = '50px';   
                        })
                        .catch( function(error) {
                        console.error( error );
                        }); */
            } else {
                  $('.organisation-details').hide()
            }
      });
      $(document).on('change', '#waiver_form_type', function (event) {
            if($(this).val() == 'text'){
                  document.querySelectorAll('.waiver_form_content_editor').forEach(editor => {
                        ClassicEditor
                              .create( editor, {
                                    toolbar: [
                                    'heading',
                                    'bold', 'italic', 'underline',
                                    'bulletedList', 'numberedList',
                                    'link',
                                    'undo', 'redo'
                                    ]
                              } )
                              .then( function(editor) {
                              editor.ui.view.editable.element.style.height = '50px';   
                              })
                              .catch( function(error) {
                              console.error( error );
                              });
                  });
            } else if($(this).val() == 'default'){
                  document.querySelectorAll('.waiver_form_content_editor').forEach(editor => {
                        ClassicEditor
                              .create( editor, {
                                    toolbar: [
                                    'heading',
                                    'bold', 'italic', 'underline',
                                    'bulletedList', 'numberedList',
                                    'link',
                                    'undo', 'redo'
                                    ]
                              } )
                              .then( function(editor) {
                                    editor.isReadOnly = true;
                                    editor.ui.view.editable.element.style.height = '50px';   
                              })
                              .catch( function(error) {
                              console.error( error );
                              });
                  });
            }
      });

      
      fieldmasking();

      if($('.ckeditor').length){
            $('.ckeditor').each(function () {
                  ClassicEditor
                        .create(this, {
                              toolbar: [
                              'heading',
                              'bold', 'italic', 'underline',
                              'bulletedList', 'numberedList',
                              'link',
                              'undo', 'redo'
                              ]
                        })
                        .then(editor => {
                              console.log('CKEditor initialized for:', this.name);
                              // Optional: Save each editor instance if needed
                              $(this).data('ckeditorInstance', editor);
                        })
                        .catch(error => {
                              console.error('Error initializing CKEditor:', error);
                        });
            });
      }
      if($('.ckeditor-classic-disabled').length){
            document.querySelectorAll('.ckeditor-classic-disabled').forEach(editor => {
                  ClassicEditor
                        .create( editor, {
                              toolbar: [
                              'heading',
                              'bold', 'italic', 'underline',
                              'bulletedList', 'numberedList',
                              'link',
                              'undo', 'redo'
                              ]
                        } )
                        .then( function(editor) {
                              editor.isReadOnly = true;
                              editor.ui.view.editable.element.style.height = '50px';   
                        })
                        .catch( function(error) {
                        console.error( error );
                        });
            });
      }
      $('.get-slug').on('change', function () {
		let context 	= $(this);
		let slug_target   = $(this).data('slugtarget');
		let existingid    = $(this).data('existingid') || 0;
		let get_slug_url  = $(this).data('url');
		if($(slug_target).val() && existingid != 0){
			return;
		} 
		$.ajax({
			url : get_slug_url,
			method : 'POST',
			data : {
				'existing_id'  : existingid,
				'slug_value'   : $(this).val(),
                        'current_slug' : $(slug_target).val(),
				'_token'       : $('meta[name="csrf-token"]').attr('content')
			},
			success : function (response) {
				if (response.status) {
					let slug = response.data.slug;
					$(`${slug_target}`).val(slug);
					
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
		
	});
      
	
	$('#validate_event_slug').click(function(){
		var url = $(this).attr('ajaxurl');
		var event_id = $(this).attr('event_id');
		var event_slug = $('.event_slug').val();
		if(!event_slug){
			showError('Event slug is required to validate.', 'error');
			return;
		}
		var data = {
			'event_id' : event_id,
			'slug' : event_slug
		};
		$.ajax({
			url: url,
			method: 'POST',
			data: data,
			success: function (response) {
				if (response.status) {
					showError(response.message, 'success');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
});
function callImaskForEmail(id) {
      IMask(document.getElementById(id), {
            mask: value => {
                  var el = document.getElementById(id);
                  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                  el.setCustomValidity("");
                  if (!el.value.match(re)) {
                        el.setCustomValidity("Please include an '@' and '.' with valid value in the email address.");
                  }
            }
      });
}
function populateData(modelId, data) {  
      $(`#${modelId}`).find('img').attr('src' , '').addClass('d-none');
      $(`#${modelId}`).find('input,textarea,select').not('[name="_token"]').val('');
      for(index in data){
            console.log(index);
            let item = data[index];
            if(!item)
                  continue;
            if(index == 'uploaded_logo'){
                  $(`#${modelId}`).find(`#${index}`).attr('src', item).removeClass('d-none');
            }else if(index=='total_organised_events' || index=='total_cohosting_events' || index=='total_volunteered_events'){
                  $(`#${modelId}`).find(`#${index}`).text(item=='N/a'?0:item);
            }else{
                  $(`#${modelId}`).find(`#${index}`).val(item); 
            }
      } 
}
/* function changeTripDateModal(booking_id){
      var url = base + '/get-booking-for-change-dates/'+booking_id;
      var data = {};
      callAjax(url, data, function (result) {
            if (result.status) {
                  $('#change_booking_date_start_datetime').val(result.data.trip_start_dt)
                  $('#change-booking-date-modal-tbody').html(result.data.html);
                  $('#change-booking-date-modal').modal('show');
                  $('#check_start_date_booking_id').val(booking_id);
            }
      });
} */
function submitDropImage(){
      if($('#v_drop_km').val()==''){
            showError('Enter drop time KM from vehicle.', 'error');
      } else if($('#v_drop_time_condition').prop('checked')){
            var is_img_updated = false;
            if($('#v_drop_front_image_div').find('a.btn-success').length){
                  is_img_updated = true;
            } else if($('#v_drop_back_image_div').find('a.btn-success').length){
                  is_img_updated = true;
            } else if($('#v_drop_left_image_div').find('a.btn-success').length){
                  is_img_updated = true;
            } else if($('#v_drop_right_image_div').find('a.btn-success').length){
                  is_img_updated = true;
            } else if($('#v_drop_special_image1_div').find('a.btn-success').length){
                  is_img_updated = true;
            }
            if(!is_img_updated){
                  showError('Please upload at least one image if you find any issue with the vehicle.', 'error');
            } else {
                  $('#formDelivery').submit();
            }
      } else {
            $('#formDelivery').submit();
      }
}
function activaTab(tab){
    $(tab).tab('show');
    $("html, body").animate({ scrollTop: 0 }, "slow");
};
function hideShowLoader(state){
      if(state){
      $('#de-preloader').addClass('loadershow');
      }else{
      $('#de-preloader').removeClass('loadershow');
      }
}
function callActDropDatetime(){
  $('.vehicle_actual_drop_datetime').datetimepicker({
        format:'Y-m-d H:i',
        minTime:'07:00',
        maxTime:'21:00',
        step:30,
        onClose:function(ct,e){
              vehicle_tr_change($(e).attr('vid'));
        }
  });
      /* $('.vehicle_est_drop_datetime').datetimepicker({
        format:'Y-m-d H:i',
        minTime:'07:00',
        maxTime:'21:00',
        step:30,
        onClose:function(ct,e){
              vehicle_tr_change($(e).attr('vid'),true);
        }
      }); */
}
function validateEmail(email) {
  var re = /\S+@\S+\.\S+/;
  return re.test(email);
}
function callAjax(url, dataset, callbackfun, async_type = false) {
  $.ajax({
        type: "POST",
        url: url,
        data: dataset,
        async: async_type,
        success: function (response) {
              callbackfun(response);
        }
  });
}
function showError(message = '', toastType = 'danger') {
  if (toastType == 'error') {
        toastType = 'danger';
  }
  if (!message)
        return;
  //One.helpers('jq-notify', {type: toastType , message: message});
  var success_toast = document.getElementById("success_toast");
  var error_toast = document.getElementById("error_toast");
  if (toastType == 'danger') {
        $('#error_toast strong').html(message);
        var toast = new bootstrap.Toast(error_toast)
        toast.show()
  }
  else {
        $('#success_toast strong').html(message);
        var toast = new bootstrap.Toast(success_toast)
        toast.show()
  }
}
function readURL(input) {
  console.log($(input).attr('id'));
  if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
              $('[for="'+$(input).attr('id')+'"]').text('Uploaded');
        }
        reader.readAsDataURL(input.files[0]);
  }
}
function callSellStartEndDatePicker(){
	$('.tk_sell_start_date').datetimepicker({
		scrollMonth: false,
		scrollInput: false,
		format: 'Y-m-d',
		timepicker: false,
		onShow: function (ct) {
			var minDateForSellDate = false;
			if (jQuery('#site_publish_date').val() != '') {
				var dateEnd = new Date(jQuery('#site_publish_date').val());
				minDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
			}
			this.setOptions({
				minDate: minDateForSellDate ? minDateForSellDate : false
			})
			var maxDateForSellDate = false;
			if (jQuery('#end_datetime').val() != '') {
				var dateEnd = new Date(jQuery('#end_datetime').val());
				dateEnd.setDate(dateEnd.getDate() - 1);
				maxDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
			}
			this.setOptions({
				maxDate: maxDateForSellDate ? maxDateForSellDate : false
			})
		},
	});
	$('.tk_sell_end_date').datetimepicker({
		scrollMonth: false,
		scrollInput: false,
		format: 'Y-m-d',
		timepicker: false,
		onShow: function (ct) {
			var minDateForSellDate = false;
			if (jQuery('#site_publish_date').val() != '') {
				var dateEnd = new Date(jQuery('#site_publish_date').val());
				minDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
			}
			this.setOptions({
				minDate: minDateForSellDate ? minDateForSellDate : false
			})
			var maxDateForSellDate = false;
			if (jQuery('#end_datetime').val() != '') {
				var dateEnd = new Date(jQuery('#end_datetime').val());
				dateEnd.setDate(dateEnd.getDate() - 1);
				maxDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
			}
			this.setOptions({
				//maxDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():false
				maxDate: maxDateForSellDate ? maxDateForSellDate : false
			})
		},
	});
}
function fieldmasking(){
      if($('.usphonemask').length != 0){
            document.querySelectorAll('.usphonemask').forEach(phonefield => {
                  var phoneMask = IMask(phonefield, {
                        mask: '+{1} 000-000-0000'
                  });
            });
      }
      if($('.textonlymask').length != 0){
            document.querySelectorAll('.textonlymask').forEach(textfield => {
                  var textMask = IMask(textfield, {
                        mask: /^[a-zA-Z\s]*$/
                  });
            });
      }
      if($('.pincodemask').length != 0){
            document.querySelectorAll('.pincodemask').forEach(pincodefield => {
                  var pincodeMask = IMask(pincodefield, {
                        mask: /^[A-Za-z0-9\s]*$/
                  });
            });
      }
      if($('.onlyTextNumberSplaceMask').length != 0){
            document.querySelectorAll('.onlyTextNumberSplaceMask').forEach(pincodefield => {
                  var pincodeMask = IMask(pincodefield, {
                        mask: /^[A-Za-z0-9\s]*$/
                  });
            });
      }
      if($('.slugmask').length != 0){
            document.querySelectorAll('.slugmask').forEach(slugfield => {
                  var slugMask = IMask(slugfield, {
                        mask: /^[a-z0-9-]*$/,
                        prepare: function (str) {
                              return str.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
                        }
                  });
            });

      }
      if($('.urlmask').length != 0){
            document.querySelectorAll('.urlmask').forEach(urlfield => {
                  var urlMask = IMask(urlfield, {
                        mask: 'https://{rest}',
                        blocks: {
                              rest: {
                                    mask: /^[a-zA-Z0-9./?&=_-]*$/,  // allows valid URL characters
                                    prepare: function (str) {
                                    return str.replace(/\s+/g, ''); // remove spaces
                                    }
                              }
                        }
                  });
            });

      }
}