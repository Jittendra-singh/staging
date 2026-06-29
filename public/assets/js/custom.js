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
                  422: function (responseObject, textStatus, jqXHR) {
                        let responseText = JSON.parse(responseObject.responseText);
                        showError(responseText.message, 'error');
                  },
                  405: function (responseObject, textStatus, jqXHR) {
                        let responseText = JSON.parse(responseObject.responseText);
                        showError(responseText.message, 'error');
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
                  if (datatype == 'organiser') {
                        url = base + '/get-organizer-options';
                  } else if (datatype == 'event') {
                        url = base + '/get-event-options';
                  } else if (datatype == 'volunteer') {
                        url = base + '/get-volunteer-options';
                  } else if (datatype == 'all_volunteers') {
                        url = base + '/get-all-volunteer-options';
                  } else if (datatype == 'pincode') {
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
                  format: 'Y-m-d',
                  timepicker: false,
            });
      }
      if ($('.only_datetime_picker').length) {
            $('.only_datetime_picker').datetimepicker({
                  format: 'Y-m-d H:i',
                  step: 30,
            });
      }
      //if($('#start_datetime').length && $('#site_publish_date').length && $('#ticket_sell_start_time').length){
      if ($('#start_datetime').length && $('#site_publish_date').length) {
            $('#start_datetime').datetimepicker({
                  format: 'Y-m-d, H:i',
                  step: 30,
                  minDate: 0,
                  onShow: function (ct) {
                        this.setOptions({
                              //maxDate:jQuery('#end_datetime').val()?jQuery('#end_datetime').val():false
                        })
                  },
			onChangeDateTime: function (ct, $input) {
				/* if ($('#end_datetime').val() != '') {
					var startDate = new Date(ct);
					var endDate = new Date($('#end_datetime').val());
					var diffTime = Math.abs(endDate - startDate);
					var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
					if (diffDays >= 7) {
						$('#longerThanOneWeekText').show();
					} else {
						$('#longerThanOneWeekText').hide();
					}
				} */
				/* var startDateTimeValue = $input.val();
				if (startDateTimeValue != '') {
					$('#end_datetime').val(startDateTimeValue).trigger('change');
					$('#longerThanOneWeekText').hide();
				} */
                        var startDateTimeValue = $input.val();
                        if (startDateTimeValue != '') {
                              // clone ct so we don't mutate the picker's internal date
                              var endDateTime = new Date(ct.getTime());
                              endDateTime.setHours(endDateTime.getHours() + 2);

                              // format back to 'Y-m-d, H:i'
                              var year = endDateTime.getFullYear();
                              var month = (endDateTime.getMonth() + 1).toString().padStart(2, '0');
                              var day = endDateTime.getDate().toString().padStart(2, '0');
                              var hours = endDateTime.getHours().toString().padStart(2, '0');
                              var minutes = endDateTime.getMinutes().toString().padStart(2, '0');

                              var formattedEndDateTime = year + '-' + month + '-' + day + ', ' + hours + ':' + minutes;

                              $('#end_datetime').val(formattedEndDateTime).trigger('change');
                              $('#longerThanOneWeekText').hide();
                        }
			}
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
                  format: 'Y-m-d, H:i',
                  step: 30,
                  onShow: function (ct) {
                        this.setOptions({
                              minDate: jQuery('#start_datetime').val() ? jQuery('#start_datetime').val() : 0
                        })
                  },
                  onChangeDateTime: function (ct) {
                        if (ct) {
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
                  format: 'Y-m-d',
			minDate: 0,
                  timepicker: false,
                  onShow: function (ct) {
                        this.setOptions({
                              maxDate: jQuery('#start_datetime').val() ? jQuery('#start_datetime').val() : false
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
                  format: 'Y-m-d',
                  timepicker: false,
                  onShow: function (ct) {
                        this.setOptions({
                              minDate: jQuery('#end_datetime').val() ? jQuery('#end_datetime').val() : false
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
            $('.forgot-password-modal form').attr('action', route);
      })
      $('.generate-event-meta-data').on('click', async function (event) {
            $('#focus_keywords, #meta_title, #meta_description').val('').attr('disabled', 'disabled');
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
      $('.generate-focus-keywords').on('click', async function (event) {
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
            let target = $(event.target);
            let route = $(target).data('action');
            let clonedElement = $('.ticket-form-replica .ticket-form').clone();
            let nextIndex = $('.ticket-container .ticket-form').last().length ? $('.ticket-container .ticket-form').last().find('.current_counter').val() || -1 : -1;
            nextIndex = parseInt(nextIndex) + 1;
            $(clonedElement).find('.current_counter').val(nextIndex);
            $(clonedElement).find('.form-ele').each(function () {
                  let name = $(this).attr('name');
                  name = name.replace('counter_placeholder', nextIndex);
                  $(this).attr('name', name);
            });

            $(clonedElement).find('[data-bs-target="#collapse_counter_placeholder"]').attr('aria-controls', `collapse_${nextIndex}`);
            $(clonedElement).find('[data-bs-target="#collapse_counter_placeholder"]').attr('data-bs-target', `#collapse_${nextIndex}`);
            $(clonedElement).find('#collapse_counter_placeholder').attr('id', `collapse_${nextIndex}`);
            $(clonedElement).find('#collapse_title_counter_placeholder').attr('id', `collapse_title_${nextIndex}`);
            $(clonedElement).find(`#collapse_title_${nextIndex}`).text(nextIndex + 1);

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
      if ($('#organisation_short_description').length) {
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
                  .create(editor, {
                        toolbar: [
                              'heading',
                              'bold', 'italic', 'underline',
                              'bulletedList', 'numberedList',
                              'link',
                              'undo', 'redo'
                        ]
                  })
                  .then(function (editor) {
                        editor.ui.view.editable.element.style.height = '50px';
                  })
                  .catch(function (error) {
                        console.error(error);
                  });
      }
      $(document).on('change', '[name="is_organiser"]', function (event) {
            // onchange="$(this).val() == 1 ? $('.organisation-details').show() : $('.organisation-details').hide()"
            if ($(this).val() == 1) {
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
            if ($(this).val() == 'text') {
                  document.querySelectorAll('.waiver_form_content_editor').forEach(editor => {
                        ClassicEditor
                              .create(editor, {
                                    toolbar: [
                                          'heading',
                                          'bold', 'italic', 'underline',
                                          'bulletedList', 'numberedList',
                                          'link',
                                          'undo', 'redo'
                                    ]
                              })
                              .then(function (editor) {
                                    editor.ui.view.editable.element.style.height = '50px';
                              })
                              .catch(function (error) {
                                    console.error(error);
                              });
                  });
            } else if ($(this).val() == 'default') {
                  document.querySelectorAll('.waiver_form_content_editor').forEach(editor => {
                        ClassicEditor
                              .create(editor, {
                                    toolbar: [
                                          'heading',
                                          'bold', 'italic', 'underline',
                                          'bulletedList', 'numberedList',
                                          'link',
                                          'undo', 'redo'
                                    ]
                              })
                              .then(function (editor) {
                                    editor.isReadOnly = true;
                                    editor.ui.view.editable.element.style.height = '50px';
                              })
                              .catch(function (error) {
                                    console.error(error);
                              });
                  });
            }
      });


      fieldmasking();

      if ($('.ckeditor').length) {
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
      if ($('.ckeditor-classic-disabled').length) {
            document.querySelectorAll('.ckeditor-classic-disabled').forEach(editor => {
                  ClassicEditor
                        .create(editor, {
                              toolbar: [
                                    'heading',
                                    'bold', 'italic', 'underline',
                                    'bulletedList', 'numberedList',
                                    'link',
                                    'undo', 'redo'
                              ]
                        })
                        .then(function (editor) {
                              editor.isReadOnly = true;
                              editor.ui.view.editable.element.style.height = '50px';
                        })
                        .catch(function (error) {
                              console.error(error);
                        });
            });
      }
      $('.get-slug').on('change', function () {
            let context = $(this);
            let slug_target = $(this).data('slugtarget');
            let existingid = $(this).data('existingid') || 0;
            let get_slug_url = $(this).data('url');
            if ($(slug_target).val() && existingid != 0) {
                  return;
            }
            $.ajax({
                  url: get_slug_url,
                  method: 'POST',
                  data: {
                        'existing_id': existingid,
                        'slug_value': $(this).val(),
                        'current_slug': $(slug_target).val(),
                        '_token': $('meta[name="csrf-token"]').attr('content')
                  },
                  success: function (response) {
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


      $('#validate_event_slug').click(function () {
            var url = $(this).attr('ajaxurl');
            var event_id = $(this).attr('event_id');
            var event_slug = $('.event_slug').val();
            if (!event_slug) {
                  showError('Event slug is required to validate.', 'error');
                  return;
            }
            var data = {
                  'event_id': event_id,
                  'slug': event_slug
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
      $('.showWidgetDetailBtn').click(function () {
            var url = $(this).attr('url');
            $.ajax({
                  url: url,
                  method: 'POST',
                  data: {},
                  success: function (response) {
                        if (response.status) {
                              $('#showWidgetDetailBody').html(response.data);
                              $('#showWidgetDetail').modal('show');
                        }
                        else {
                              showError(response.message, 'error');
                        }
                  }
            });
      });

      $(document).on('click', '#copyWidgetCode', function () {
            var widgetCode = $('#widgetCode').text();
            navigator.clipboard.writeText(widgetCode).then(function () {
                  showError('Widget code copied.', 'success');
            });
      });

      $('.remove-cohost').click(function () {
            var url = $(this).attr('url');
            var control = this;
            Swal.fire({
                  title: "Are you sure?",
                  text: `You want to remove this co-host from event?`,
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, remove it!'
            }).then((willDelete) => {
                  if (willDelete.isConfirmed) {
                        $(control).attr('disabled', 'disabled');
                        $.ajax({
                              url: url,
                              method: 'POST',
                              data: {},
                              success: function (response) {
                                    if (response.status) {
                                          showError(response.message, 'success');
                                          setTimeout(() => {
                                                window.location.reload();
                                          }, 350);
                                    }
                                    else {
                                          showError(response.message, 'error');
                                    }
                              }
                        });
                  }
            });
      });
      
	$(document).on('click','.remove-event-img-btn',function(){
		var control = this;
		var url = $(control).attr('url');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				if (response.status) {
					if(response.data){
						$('#savedImages').html(response.data);
					} else {
						$('#savedImages').html('');
					}
					showError(response.message, 'success');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
      
	$('.send-thankyou-request-email-btn').on('click', function () {
		var control = this;
		Swal.fire({
			title: "Are you sure?",
			text: 'You want to send the wrap-up & certificate emails to all attendees. Event will be marked as completed after sending the email.',
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, send it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				let event_id = $(control).attr('event_id');
				let ajaxurl = $(control).attr('ajaxurl');
				$.ajax({
					url: ajaxurl,
					method: 'POST',
					data: {
						'event_id': event_id,
						'_token': $('meta[name="csrf-token"]').attr('content')
					},
					success: function (response) {
						if (response.status) {
							showError(response.message, 'success');
							setTimeout(() => {
								window.location.reload();
							}, 350);
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
      
	$('#reschedule_now_btn').click(function () {
		const form = document.getElementById('rescheduleEventForm');
		if (!form.checkValidity()) {
			// This will show native browser validation messages
			form.reportValidity();
			return;
		}
		Swal.fire({
			title: "Are you sure?",
			text: `You want to reschedule this event?`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, reschedule it!'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
                        $('#reschedule_now_btn').attr('disabled', 'disabled');
				$('#rescheduleEventForm').submit();
			}
		});
	});
	$(document).on('click', '.show-alert-run-url', function () {
		var control = this;
		var message = $(control).attr('message');
		var url = $(control).attr('url');
		Swal.fire({
			title: "Are you sure?",
			text: message,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes'
		}).then((willDelete) => {
			if (willDelete.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
						}
						else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
      
	$('.submit-send-wrapup-btn').on('click', function () {
		/* if($('#waste_collected').val()==''){
			showError('Please enter the waste collected value', 'error');
			$('#waste_collected').focus();
			return false;
		}
		if($('#area_cleaned').val()==''){
			showError('Please enter the area cleaned value', 'error');
			$('#area_cleaned').focus();
			return false;
		} */
		var control = this;
		var url = $(this).attr('url');
		$(control).attr('disabled', 'disabled');
		$(control).text('Finalizing...');
		$.ajax({
			url: url,
			method: 'POST',
			data: {},
			success: function (response) {
				if (response.status) {
					Swal.fire({
						title: "Before You Submit",
						//text: response.data,
						html: response.data,
						icon: "warning",
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Submit Anyway'
					}).then((willDelete) => {
						if (willDelete.isConfirmed) {
							$('#send_cleanup_report_email').val(1);
							$('#submit-cleanup-report-btn').trigger('click');
						} else {
							$(control).removeAttr('disabled');
							$(control).text('Save & Finalize Cleanup Report');
						}
					});
				}
				else {
					$(control).removeAttr('disabled');
					$(control).text('Save & Finalize Cleanup Report');
					showError(response.message, 'error');
				}
			}
		});
	});
	if($('#after_event_images').length){
		const MAX_IMAGES = 4;
		let selectedFiles = [];

		// Preview new images on select
		$('#after_event_images').on('change', function () {
			const self = this;
			const newFiles = Array.from(this.files);
			const savedCount = $('#savedImages .position-relative').length;
			const validCount = selectedFiles.filter(f => f !== null).length;
			const totalAllowed = MAX_IMAGES - savedCount - validCount;

			if (totalAllowed <= 0) {
				showError('You can only upload a maximum of 4 images.', 'error');
				$(self).val(''); // safe to clear because rebuildFileInput() will restore valid files
				rebuildFileInput(); // restore previously selected valid files back to input
				return;
			}

			let added = 0;
			let limitReached = false;

			newFiles.forEach(function (file) {
				if (added >= totalAllowed) {
					if (!limitReached) {
						showError('You can only upload a maximum of 4 images.', 'error');
						limitReached = true;
					}
					return;
				}

				const reader = new FileReader();
				const index = selectedFiles.length;
				//var index = Math.floor(10000 + Math.random() * 90000);
				selectedFiles.push(file);
				added++;

				reader.onload = function (e) {
					const wrapper = `
						<div class="position-relative me-3 mt-2" id="new-img-${index}">
							<img src="${e.target.result}" style="width:50px;height:50px;border-radius:6px;object-fit:cover;">
							<button type="button" class="remove-new-img" data-index="${index}">✕</button>
						</div>
					`;

					/* if ($('#savedImages').length === 0) {
						$('#after_event_images').closest('.form-group').append(
							'<div id="savedImages" class="d-flex flex-wrap align-items-center mt-2"></div>'
						);
					}
					$('#savedImages').append(wrapper); */
					if ($('#newImagePreview').length === 0) {
						$('#after_event_images').closest('.form-group').append(
							'<div id="newImagePreview" class="d-flex flex-wrap align-items-center mt-2"></div>'
						);
					}
					$('#newImagePreview').append(wrapper);
				};

				reader.readAsDataURL(file);
			});

			rebuildFileInput();
		});

		// Remove newly selected (unsaved) image
		$(document).on('click', '.remove-new-img', function () {
			const index = parseInt($(this).data('index'));
			selectedFiles[index] = null;
			$('#new-img-' + index).remove();
			rebuildFileInput();
		});

		// Rebuild file input after removal using DataTransfer
		function rebuildFileInput() {
			const validFiles = selectedFiles.filter(f => f !== null);
			const dataTransfer = new DataTransfer();
			validFiles.forEach(function (file) {
				dataTransfer.items.add(file);
			});
			$('#after_event_images')[0].files = dataTransfer.files;
		}
	}
      
	// ── APPROVE CERTIFICATE ───────────────────────────────────────────────────
	$(document).on('click', '.btn-approve-certificate', function () {
		var url  = $(this).attr('url');
		var name = $(this).attr('data-name');
		var control = this;

		Swal.fire({
			title: "Approve Certificate?",
			html: `You are approving the certificate request for <b>${name}</b>. This action cannot be undone.`,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, approve it!'
		}).then((result) => {
			if (result.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
							setTimeout(() => { window.location.reload(); }, 1000);
						} else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});

	// ── REJECT CERTIFICATE ────────────────────────────────────────────────────
	$(document).on('click', '.btn-reject-certificate', function () {
		var url  = $(this).attr('url');
		var name = $(this).attr('data-name');
		var control = this;

		Swal.fire({
			title: "Reject Certificate Request?",
			html: `You are rejecting the certificate for <b>${name}</b>.`,
			input: "textarea",
			inputPlaceholder: "Enter reason for rejection...",
			inputAttributes: {
				rows: 4,
				style: "resize:none;"
			},
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Reject',
			cancelButtonText: 'Cancel',
			inputValidator: (value) => {
				if (!value || !value.trim()) {
					return 'Please enter a reason for rejection.';
				}
			}
		}).then((result) => {
			if (result.isConfirmed) {
				$(control).attr('disabled', 'disabled');
				$.ajax({
					url: url,
					method: 'POST',
					data: {
						reject_reason: result.value
					},
					success: function (response) {
						$(control).removeAttr('disabled');
						if (response.status) {
							showError(response.message, 'success');
							setTimeout(() => { window.location.reload(); }, 1000);
						} else {
							showError(response.message, 'error');
						}
					}
				});
			}
		});
	});
      
	
	$(document).on('click', '.btn-resend-waiver-modal', function () {
		var control = this;
		var url = $(control).attr('url');
		var checkurl = $(control).attr('checkurl');
		var defaultemail = $(control).attr('defaultemail');
		$(control).attr('disabled', 'disabled');
		$.ajax({
			url: checkurl,
			method: 'POST',
			data: {},
			success: function (response) {
				$(control).removeAttr('disabled');
				if (response.status) {
					var log = response.data.log;
					var lrwlType = '';
					var lrwlEmail = '';
					var lrwlDatetime = '';
					if(log!=''){
						lrwlType = log.link_send_type==1?'Original':'Alternative' || '';
						lrwlEmail = log.link_send_to_email || '';
						lrwlDatetime = log.createdDateTime || '';
					}
					if(defaultemail.trim()==''){
						defaultemail = 'NO EMAIL';
					}
					$('#resendWaiverEmailModal').find('form').attr('action', url);
					$('#resend_waiver_default_email').text(defaultemail);
					$('#alternative_waiver_email').val('');

					if (lrwlType.trim() != '' || lrwlEmail.trim() != '' || lrwlDatetime.trim() != '') {
						$('#resend_waiver_last_log_empty').hide();
						$('#resend_waiver_last_log_content').show();
						$('#resend_waiver_last_log_type').text(lrwlType);
						$('#resend_waiver_last_log_email').text(lrwlEmail);
						$('#resend_waiver_last_log_datetime').text(lrwlDatetime);
					} else {
						$('#resend_waiver_last_log_content').hide();
						$('#resend_waiver_last_log_empty').show();
						$('#resend_waiver_last_log_type').text('');
						$('#resend_waiver_last_log_email').text('');
						$('#resend_waiver_last_log_datetime').text('');
					}

					selectResendOption(1);
					$('#resendWaiverEmailModal').modal('show');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$(document).on('click', '.btn-resend-waiver-logs-modal', function () {
		var logs = $(this).data('logs');

		if (typeof logs === 'string') {
			try {
				logs = JSON.parse(logs);
			} catch (e) {
				logs = [];
			}
		}

		var rows = '';

		if (logs && logs.length > 0) {
			logs.forEach(function (log) {
				rows += `
					<tr>
						<td><span class="resend-waiver-log-type">${escapeHtml(log.type || '')}</span></td>
						<td>${escapeHtml(log.email || '')}</td>
						<td>${escapeHtml(log.send_at || '')}</td>
					</tr>
				`;
			});

			$('#resendWaiverLogsTableBody').html(rows);
			$('.resend-waiver-log-empty').hide();
			$('.resend-waiver-log-table-wrap').show();
		} else {
			$('#resendWaiverLogsTableBody').html('');
			$('.resend-waiver-log-table-wrap').hide();
			$('.resend-waiver-log-empty').show();
		}
	});
	$(document).on('click', '.submit-btn-send-waiver-email', function (e) {
		var control = this;
		var checkedRadio = $('input[name="resend_waiver_type"]:checked').val();
		var alternativeEmail = $('#alternative_waiver_email').val();
		if(checkedRadio==2 && alternativeEmail==''){
			showError('Please enter alternative email address.', 'error');
			$('#alternative_waiver_email').focus();
			return false;
		}
		$(control).text('Sending...').attr('disabled', true);
		var formData = $('#resendWaiverEmailModal').find('form').serialize();
		var url = $('#resendWaiverEmailModal').find('form').attr('action');
		$.ajax({
			url: url,
			method: 'POST',
			data: formData,
			success: function (response) {
				$(control).text('Send Email').removeAttr('disabled');
				if (response.status) {
					showError(response.message, 'success');
					$('#alternative_waiver_email').val('');
					$('#resendWaiverEmailModal').modal('hide');
				}
				else {
					showError(response.message, 'error');
				}
			}
		});
	});
	$('#cancel-all-ticket-btn').click(function () {
		$('#cancel-all-ticket-popup').modal('show');
	});
      $('.select-all-ticket-checkbox').change(function () {
		if ($(this).prop('checked')) {
			$('.attendee-ticket-checkbox').prop('checked', 'checked');
		} else {
			$('.attendee-ticket-checkbox').prop('checked', false);
		}
		calcTicketPriceForPopup();
	});
	$('.attendee-ticket-checkbox').change(function () {
		calcTicketPriceForPopup();
	});
	function calcTicketPriceForPopup() {
		var amount = 0;
		$('.attendee-ticket-checkbox').each(function (c, e) {
			if ($(e).prop('checked')) {
				var price = $(e).attr('price');
				amount += parseFloat(price);
			}
		});
		$('.ticket-cancel-amt').text(amount);
	}
});
function escapeHtml(value) {
	return $('<div>').text(value).html();
}
function selectResendOption(n) {
	$('#resend_waiver_type1, #resend_waiver_type2').prop('checked', false);
	$('#resend_waiver_type' + n).prop('checked', true);

	$('#optionCard1, #optionCard2').removeClass('selected');
	$('#radioDot1, #radioDot2').removeClass('on');
	$('#optionCard' + n).addClass('selected');
	$('#radioDot' + n).addClass('on');

	if (n === 2) {
		$('#alternative_waiver_email').prop('disabled', false).focus();
		$('#resend_waiver_note').html(
			'<span class="resend-note-label">Alternative email selected — </span>' +
			'The link goes to the address entered above only. This does <u>not</u> update the volunteer\'s profile email.'
		);
	} else {
		$('#alternative_waiver_email').prop('disabled', true).val('');
		$('#resend_waiver_note').html(
			'<span class="resend-note-label">Original email selected — </span>' +
			'The waiver link will be resent to the volunteer\'s registered address. This does not change their profile email.'
		);
	}
}
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
      $(`#${modelId}`).find('img').attr('src', '').addClass('d-none');
      $(`#${modelId}`).find('input,textarea,select').not('[name="_token"]').val('');
      for (index in data) {
            console.log(index);
            let item = data[index];
            if (!item)
                  continue;
            if (index == 'uploaded_logo') {
                  $(`#${modelId}`).find(`#${index}`).attr('src', item).removeClass('d-none');
            } else if (index == 'total_organised_events' || index == 'total_cohosting_events' || index == 'total_volunteered_events') {
                  $(`#${modelId}`).find(`#${index}`).text(item == 'N/a' ? 0 : item);
            } else {
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
function submitDropImage() {
      if ($('#v_drop_km').val() == '') {
            showError('Enter drop time KM from vehicle.', 'error');
      } else if ($('#v_drop_time_condition').prop('checked')) {
            var is_img_updated = false;
            if ($('#v_drop_front_image_div').find('a.btn-success').length) {
                  is_img_updated = true;
            } else if ($('#v_drop_back_image_div').find('a.btn-success').length) {
                  is_img_updated = true;
            } else if ($('#v_drop_left_image_div').find('a.btn-success').length) {
                  is_img_updated = true;
            } else if ($('#v_drop_right_image_div').find('a.btn-success').length) {
                  is_img_updated = true;
            } else if ($('#v_drop_special_image1_div').find('a.btn-success').length) {
                  is_img_updated = true;
            }
            if (!is_img_updated) {
                  showError('Please upload at least one image if you find any issue with the vehicle.', 'error');
            } else {
                  $('#formDelivery').submit();
            }
      } else {
            $('#formDelivery').submit();
      }
}
function activaTab(tab) {
      $(tab).tab('show');
      $("html, body").animate({ scrollTop: 0 }, "slow");
};
function hideShowLoader(state) {
      if (state) {
            $('#de-preloader').addClass('loadershow');
      } else {
            $('#de-preloader').removeClass('loadershow');
      }
}
function callActDropDatetime() {
      $('.vehicle_actual_drop_datetime').datetimepicker({
            format: 'Y-m-d H:i',
            minTime: '07:00',
            maxTime: '21:00',
            step: 30,
            onClose: function (ct, e) {
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
                  $('[for="' + $(input).attr('id') + '"]').text('Uploaded');
            }
            reader.readAsDataURL(input.files[0]);
      }
}
function callSellStartEndDatePicker() {
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
                        //dateEnd.setDate(dateEnd.getDate() - 1);
                        maxDateForSellDate = dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1) + '-' + dateEnd.getDate();
                  }
                  this.setOptions({
                        //maxDate:jQuery('#start_datetime').val()?jQuery('#start_datetime').val():false
                        maxDate: maxDateForSellDate ? maxDateForSellDate : false
                  })
            },
      });
}
function fieldmasking() {
      if ($('.usphonemask').length != 0) {
            document.querySelectorAll('.usphonemask').forEach(phonefield => {
                  var phoneMask = IMask(phonefield, {
                        mask: '+{1} 000-000-0000'
                  });
            });
      }
      if ($('.textonlymask').length != 0) {
            document.querySelectorAll('.textonlymask').forEach(textfield => {
                  var textMask = IMask(textfield, {
                        mask: /^[a-zA-Z\s]*$/
                  });
            });
      }
      if ($('.pincodemask').length != 0) {
            document.querySelectorAll('.pincodemask').forEach(pincodefield => {
                  var pincodeMask = IMask(pincodefield, {
                        mask: /^[A-Za-z0-9\s]*$/
                  });
            });
      }
      if ($('.onlyTextNumberSplaceMask').length != 0) {
            document.querySelectorAll('.onlyTextNumberSplaceMask').forEach(pincodefield => {
                  var pincodeMask = IMask(pincodefield, {
                        mask: /^[A-Za-z0-9\s]*$/
                  });
            });
      }
      if ($('.slugmask').length != 0) {
            document.querySelectorAll('.slugmask').forEach(slugfield => {
                  var slugMask = IMask(slugfield, {
                        mask: /^[a-z0-9-]*$/,
                        prepare: function (str) {
                              return str.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
                        }
                  });
            });

      }
      if ($('.urlmask').length != 0) {
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