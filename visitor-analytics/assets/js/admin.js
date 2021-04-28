jQuery(document).ready(function($){
    $('#table_visitor_country').DataTable({
         "order": [[2,"desc"]],
         "searching": false,
         "paging": false,
         "info": false,
         "responsive": true
    });
    $('#table_visitor_time').DataTable({
         "searching": false,
         "paging": false,
         "info": false,
         "responsive": true
    });

    // MODAL
    $('#visitor_result').on('click','.close-modal',function(){
      $(this).parents('.modal').fadeOut();
    });

    $('#visitor_result').on('click','.modal-header',function(e){
      e.preventDefault();
      return false;
    });

    $('#visitor_result').on('click','.modal',function(){
      $(this).fadeOut();
    });


    $('#visitor_result').on('click','.view_map',function(){
      var header = $('#view_map_modal').find('.modal-header');
      var body = $('#view_map_modal').find('.modal-body');
      header.find('h2').remove();
      header.append('<h2>'+$(this).data('location')+'</h2>');
      body.empty();
      body.append('<iframe src="'+$(this).data('url')+'" style="height:100%; width:100%;"></iframe>');
      $('#view_map_modal').css('display','block');
    });

    $('#visitor_result').on('click','.view_state_table',function(){
      var header = $('#view_state_modal').find('.modal-header');
      var body = $('#view_state_modal').find('.modal-body');
      header.find('h2').remove();
      header.append('<h2>'+$(this).data('country')+'</h2>');
      body.empty();
      body.append('<iframe id="iframe_view_state" src="'+$(this).data('url')+'" style="height:100%; width:100%;"></iframe>');
      $('#view_state_modal').css('display','block');
    });

    $('.va-input-datepicker').datepicker({
      dateFormat: "yy-mm-dd",
   		language: 'en',
    });

    $('.selectby').on('change',function(){
      if ($(this).val() == 'Month') {
        $('.search_month').css('display','block');
        $('.search_date').css('display','none');
      }else if ($(this).val() == 'Date') {
        $('.search_date').css('display','block');
        $('.sv_checkbox').each(function(){
          $(this).removeClass('selected');
        });
        $('.search_month').css('display','none');
      }
    });

   $('.pdf-tooltip').hover(function(){
      $(this).siblings('.option_tooltip').toggleClass('show');
      $(this).toggleClass('selected');
   });
})
