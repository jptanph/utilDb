/**
* @class
* 빌더의 DatePicker 를 호출합니다.
*
* @options
*   'pop_calendar'  : '.pop_calendar' , // Calendar Html class
*   'years_between' : [2007,2013]     , // 셀렉트 박스의 날짜 출력 시작년, 종료년
*   'days'          : ['일', '월', '화', '수', '목', '금', '토'],  // 날짜 년월
*   'format'        : 'yyyy-mm-dd',     // 포맷
*   'animate'       : true   ,   // animate 애니메이션 효과
*   'prefix'        : 'cal_'     // 지정된 프리픽스
*
* @example
* inputTag 에 클래스 명(.input_table) 을 직접 지정 하여 표시
* <input type="text" id="day_start" class="input_table" style="width:75px;" />
* $(".input_table").BuilderCalendar(options);
*
* imgTag 에 클래스 명(.input_table)을 지정하여 inputTag 밑에 표시
* <input type="text" id="day_start" class="input_table" style="width:75px;" /><a href="javascript:;"><img  class="img_calendar"/></a>
* $(".img_calendar").BuilderCalendar(options);
*
* @name BuilderCalendar
* @author JsYang <yakuyaku@gmail.com>
* @since 2010-03-11
* @version 1.0
*
*/

;(function($){

	var inputText;
    $.BuilderCalendar_prev   = null;
    $.BuilderCalendar_status = false;

    $.fn.BuilderCalendar = $.fn.buildercalendar = function(options)
    {
        var calendar  = null;
        var opts = null;
        var today;

        var ie6 = $.browser.msie && parseInt($.browser.version) == 6 && typeof window['XMLHttpRequest'] != "object";

        var init = function(event,el,opt)
        {
            var evt = event || window.event;
            var tgt = (evt.target) ? evt.target : evt.srcElement;
            opts = $.extend({},$.fn.BuilderCalendar.defaults, opt);

            makeCalendar(el);
            inputText = $(el);

            if(el.tagName == "IMG" && opts.target == "" )  inputText = $(el).parent().prev();
            if(opts.target != "") inputText = $(opts.target);
            if(opts.self && opts.target == "")  inputText = $(tgt);

            setPostion(inputText);

            if(opts.input_target != "" ) inputText = $(opts.input_target);

            toggleCalendar(el,tgt,inputText.val());
            $.BuilderCalendar_pre = tgt;

        };

        var setDate   = function(date)
        {
            today = new Date();
            if(date) {
                today.setFullYear(date.substring(0,4));
                var month = date.substring(5,7);
                if(month.charAt(0) == "0" )  month = parseInt(month.charAt(1))-1;
                else month = month - 1;
                today.setMonth(month);
            }

            var mm = today.getMonth()+1;
            $(".year_select  option[value='"+ today.getFullYear() +"']").attr('selected','selected');
            $(".month_select option[value='"+ mm + "']").attr('selected','selected');
        };

        var makeCalendar  = function(el)
        {
            if($("." +  opts.pop_calendar).length <= 0 ) {

                calendar = $("<div class='" + opts.pop_calendar + "'></div>").css('display','none');

                $("<div class='calendar_header'><a href='javascript:;' class='calendar_close_btn'><span>닫기</span></a></div>").appendTo(calendar);
                var calendar_container = $("<div class='calendar_cont'></div>");

                calendar_container = makeCalendarHeader(calendar_container);

                calendar_container = makeCalendarBody(calendar_container);

                calendar.append(calendar_container);

                $('body').append(calendar);

                close_bind(el);
                prev_month_bind();
                next_month_bind();

                //year_select
                $(".year_select,.month_select").bind("change", function(e){
                    makeTableBody();
                });

                if (ie6) {
                    $("<iframe src='about:blank' id='iframe_bg' src='about:blank' scrolling='no' frameborder='0' ></iframe>")
                    .css({
                        'position' : 'absolute',
                        'width'    : calendar.width() ,
                        'height'   : calendar.height(),
                        'opacity'  : '0',
                        'border'   : 'none' ,
                        'display'  : 'none'
                    }).insertBefore(calendar);
                };
            }
        };

        var day_bind   = function()
        {
            $(".clendar_body").removeClass("month_body");
            $('.calendar_day').bind('click', function(event) {
                var yy = $(".year_select  option:selected").val();
                var mm = $(".month_select option:selected").text();
                var dd = $(this).text();

                inputText.val(opts.format.replace("yyyy", yy).replace("mm",mm).replace("dd",dd));
                inputText.change();
                calendar_close();
                if(opts.callback_func) opts.callback_func(calendar); // callback_function run
            });
        };

        var week_bind = function(w)
        {
            var $week = $('.week' + w);
            var yy = $(".year_select  option:selected").val();
            var mm = $(".month_select option:selected").text();
            $week.addClass('currentweek');
            $(".clendar_body").removeClass("month_body");
            $('.weekend').click(function(){
                var start_week_day ,end_week_day;
                var is_first = true;
                $(this).children().each(function(){
                    var day = $(this).text();
                    if(is_first && day  ) {
                        is_first = false;
                        start_week_day = day;
                    }
                    if( day != "" ) end_week_day = day;
                });
                var startweek =  opts.format.replace("yyyy", yy).replace("mm",mm).replace("dd",start_week_day);
                var endweek   =  opts.format.replace("yyyy", yy).replace("mm",mm).replace("dd",end_week_day);
                inputText.val(startweek + " ~ " + endweek );
                calendar_close();
                if(opts.callback_func) opts.callback_func(calendar); // callback_function run
            }).hover( function () {
                $(this).addClass('weekhover');
              },
              function () {
                $(this).removeClass('weekhover');
              }
            );
        };

        var month_bind = function()
        {
            var yy = $(".year_select  option:selected").val();
            var mm = $(".month_select option:selected").val()-1;
            var last_day  = getLastDay(yy,mm);
            $(".clendar_body").addClass("month_body");
            mm = $(".month_select option:selected").text();
            $(".weekend").click(function(){
                var startMonth =  opts.format.replace("yyyy", yy).replace("mm",mm).replace("dd","01");
                var endMonth   =  opts.format.replace("yyyy", yy).replace("mm",mm).replace("dd",last_day);
                inputText.val(startMonth + " ~ " + endMonth );
                calendar_close();
                if(opts.callback_func) opts.callback_func(calendar); // callback_function run
            });
        };

        var close_bind = function()
        {
            //close
            $('.calendar_close_btn').bind("click", function(e){
               if($.BuilderCalendar_status) calendar_close();
            });
        };

        var prev_month_bind = function()
        {
            //$('.prev_month').unbind('click');
            //prev_month
            $('.prev_month').bind("click", function(e){

                var yy = $(".year_select  option:selected").val();
                var mm = $(".month_select option:selected").val();

                if( mm == "1" ) {
                    yy = parseInt(yy) - 1;
                    $(".year_select option[value='"+ yy +"']").attr('selected','selected');
                    $(".month_select option[value='12']").attr('selected','selected');
                } else {
                    mm = parseInt(mm) - 1;
                    $(".month_select option[value='"+ mm +"']").attr('selected','selected');
                }

                makeTableBody();
            });
        };

        var next_month_bind = function()
        {
            //$('.next_month').unbind('click');
            //next_month
            $('.next_month').bind("click", function() {

                var yy = $(".year_select  option:selected").val();
                var mm = $(".month_select option:selected").val();

                if( mm == "12" ) {
                    yy = parseInt(yy) + 1;
                    $(".year_select option[value='"+ yy +"']").attr('selected','selected');
                    $(".month_select option[value='1']").attr('selected','selected');
                } else {
                    mm = parseInt(mm) + 1;
                    $(".month_select option[value='"+ mm +"']").attr('selected','selected');
                }

                makeTableBody();
            });

        };

        var makeCalendarHeader = function(container)
        {
            var day_select;
            var selecter;

            day_select  = $("<ul class='day_select'></ul>");
            $("<li class='prev'><a href='javascript:;' class='prev_month'><span>이전으로</span></a></li>").appendTo(day_select);

            selecter = $("<li></li>)").append(makeSelectYear()).append(makeSelectMonth());
            day_select.append(selecter);

            $("<li class='next'><a href='javascript:;' class='next_month'><span>다음으로</span></a></li>").appendTo(day_select);
            return container.append(day_select);
        };

        var makeCalendarBody  = function(container)
        {
            $("<div class='clear'></div>").appendTo(container);
            var $table = $("<table border='0' cellpadding='0' cellspacing='0' class='calendar'><thead></thead><tbody class='clendar_body'></tbody></table>");
            var $thead = $table.find("thead");
            var $tr    = $("<tr></tr>");
            var $th    = "";

            for (var i=0; i<opts.days.length; i++) {
                var classz = "";
                if( i == 0 ) classz = "sunday";
                else if (i == 6) classz = "saturday";
                $th += "<th class='" + classz + "'>" + opts.days[i] + "</th>";
            }
            $tr.append($th);
            $thead.append($tr);

            return container.append($table);;
        };

        var makeTableBody = function()
        {
            var yy = $(".year_select  option:selected").val();
            var mm = $(".month_select option:selected").val()-1;

            var first_day = getFirstDay(yy,mm);
            var last_day  = getLastDay(yy,mm);
            var day = 1;
            var firstWeek = true;
            var $tBody    = $(".clendar_body");
            var currentWeek = 0;
            $tBody.empty();

            $("<tr><td colspan='7' class='line_bt'></td></tr>").appendTo($tBody);
            var j=1;
            while(day <= last_day)
            {
                var t_loop = "<tr class='weekend week" + j+ "'>";
                for (var w = 0; w < 7; w++)
                {
                    var dayText;
                    var tdClass;
                    var now  = new Date();

                    if (w == 0) tdClass ='sunday';
                    else if(w == 6) tdClass ='sat';
                    else tdClass = "noromal";

                    if( now.getFullYear() == yy && now.getMonth() == mm && now.getDate() == day ) {
                        if(w == 0 ) tdClass = "sunday_now";
                        else tdClass = "now";
                        currentWeek = j;
                    }

                    if( (firstWeek && ( w < first_day )) || day > last_day ) {
                        dayText = "";
                        tdClass = "";
                    } else {
                        dayText = day;
                        if(day < 10 ) dayText = "0" + dayText;
                        day++;
                    }

                    t_loop += "<td class='" + tdClass + "'><a href='javascript:;' class='calendar_day'>" + dayText + "</a></td>";

                }
                t_loop += "</tr>";

                $(t_loop).appendTo($tBody);
                firstWeek = false;
                j++;
            };

            if(opts.display_type == "week" ) {
                week_bind(currentWeek);
            } else if(opts.display_type == "month") {
                month_bind();
            } else {
                day_bind(); //eventBind
            }
        };

        var getFirstDay = function(year,month)
        {
            dt = new Date(year,month,1);
            return dt.getDay();
        };

        var getLastDay = function(year, month)
        {
            var dates = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            if ((year % 4) == 0) dates[1] = 29;
            return dates[month];
        };

        var makeSelectYear  = function(yy)
        {
            var year_select  = $("<select class='year_select'></select>").width(55);
            var year_options = "";
            for (var i=opts.years_between[1]; i > opts.years_between[0]; i--) {
                var yy_selected ="";
                if(i == yy ) yy_selected = "selected='selected'";
                year_options +="<option value='"+ i + "' " + yy_selected +  ">" + i + "</option>";
            }
            return year_select.append(year_options);
        };

        var makeSelectMonth = function(mm)
        {
            var month_select  = $("<select class='month_select'></select>").width(40);
            var month_options = "";
            for (var i=1; i<=12; i++) {
                var mm_selected ="";
                var value;
                if(i == mm ) mm_selected = "selected='selected'";
                if(i < 10 ) value  = "0" + i.toString();
                else value = i;
                month_options += "<option value='"+ i + "' " + mm_selected + ">" + value + "</option>";
            }
            return month_select.append(month_options);
        };

        var toggleCalendar = function(el,tgt,wk_date)
        {
            if($.BuilderCalendar_status == false ) {
                calendar_open(el,wk_date);
            } else {
                if( $.BuilderCalendar_prev == tgt ) {
                   calendar_close();
                } else  {
                    $('.' + opts.pop_calendar).hide();
                    calendar_open(el,wk_date);
                }
            }
        };

        var is_date =  function(dt)
        {
            if(!dt) return false;
            return /^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})$/.test($.trim(dt));
        };

        var calendar_close = function()
        {
            if(!opts.animate) $("." + opts.pop_calendar).hide();
            else $("." + opts.pop_calendar).animate({"width":"toggle","height":"toggle"},opts.duration);
            if(ie6) $('#iframe_bg').hide();
            $.BuilderCalendar_status = false;
        };

        var calendar_open = function(el, wk_date)
        {
            if(is_date(wk_date))  setDate(wk_date);
            else setDate();

            makeTableBody();

            if(!opts.animate) $("." + opts.pop_calendar).show();
            else $("." + opts.pop_calendar).animate({"width":"toggle","height":"toggle"},opts.duration);
            if(ie6) $('#iframe_bg').show();
            $.BuilderCalendar_status = true;
        };

		 var setPostion = function(el)
        {
            var iPosition = el.offset();
            var cTop    = iPosition.top + el.height() + 5; // 6 inputBox Border Size
            $("." + opts.pop_calendar).css({'left':iPosition.left, 'top' : cTop });
            if(ie6) $('#iframe_bg').css({'left':iPosition.left, 'top' : cTop });
        };
		
        //var setPostion = function(el)
       // {
			
            // var iPosition = el.offset();
            //var cTop    = iPosition.top + el.height() + 5; // 6 inputBox Border Size
           // $(".pop_calendar").css({'left': 107, 'top' : 135 });
			
			
			// $("#pg_biorythm_target_date").click(function(){
				
				// $(".pop_calendar").css({'left': 107, 'top' : 135 });
			// });
			
		
			//if(ie6) $('#iframe_bg').css({'left': 107, 'top' : 135 });
            //if(ie6) $('#iframe_bg').css({'left':iPosition.left, 'top' : cTop });
      //  };
		
		 // var setPostion = function(el)
        // {
			// $("#pg_biorythm_birth_date").click(function(){
				// var iPosition = el.offset();
				// var cTop    = iPosition.top + el.height() + 5; // 6 inputBox Border Size
				// $("." + opts.pop_calendar).css({'left':iPosition.left, 'top' : cTop });
				// if(ie6) $('#iframe_bg').css({'left':iPosition.left, 'top' : cTop });			
			
			// });
			
			// $("#pg_biorythm_target_date").click(function(){
				// var iPosition = el.offset();
				// var cLeft    = iPosition.left + el.height() - 10; // 6 inputBox Border Size
				// var cTop    = iPosition.top + el.height() + 5; // 6 inputBox Border Size
				// $("." + opts.pop_calendar).css({'left':cLeft, 'top' : cTop });
				// if(ie6) $('#iframe_bg').css({'left':cLeft, 'top' : cTop });
			// });	
		
          
        // };
		
        $(this).bind("click.Calendar", function(event){
            init(event,this,options);
            event.preventDefault(); //이벤트의 캡쳐 기본 동작 막음
        });
		
		// $(".pop_calendar").live("hover", function(event) { // When user hovers over an <li>
			 // event.preventDefault();
			// if(event.type == 'mouseenter') {
				// $(document.body).unbind("click");
			// } else {
				// $(document.body).bind("click", function(){
					// calendar_close();
				// });
			// }
		// }); 

        var setOptions = function(options)
        {
            if($.BuilderCalendar_status) calendar_close();
            $.extend($.fn.BuilderCalendar.defaults,options);
        };

        return {'setOptions' : setOptions };

    };

    //기본 설정
    $.fn.BuilderCalendar.defaults = {
        'pop_calendar'  : 'pop_calendar' ,
        'years_between' : [2007,2013]     ,
        //'days'          : ['일', '월', '화', '수', '목', '금', '토'],
        'days'          : ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
        'format'        : 'yyyy-mm-dd',
        'animate'       : true ,
        'duration'      : 'fast' ,
        'prefix'        : 'cal_' ,
        'callback_func' : false  ,
        'target'        : ""  ,
        'input_target'  : ""  ,
        'display_type'     : 'day' ,
        'self'         : false
    };

})(jQuery);
