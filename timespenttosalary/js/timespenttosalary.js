$( document ).ready(function() {


    var rows = document.querySelectorAll("#tmenu_tooltip > div > ul > li");
   // console.log(rows );
    //console.log("==========>"+$(rows).length); 
        
        var lo = 0;
        var Home = "";
        for( rax of $(rows))
        {


            console.log("=====>"+$(rax).length);
            if($(rax).length > 0)
            {
                var id_ = $(rax).prop("id");
               // if(id_!="mainmenutd_home")
                //{
                    $("#"+id_).hide();
                //}
                
                
            }

        }

        var li = '<li class="tmenusel" id="mainmenutd_menu_cust"><div class="tmenucenter"><i class="fa fa-bars" aria-hidden="true"></i></div></li>';


var li = '<li class="tmenu menuhiderhh" id="sidebarrightbutton"><div class="tmenucenter"><a class="tmenuimage" tabindex="-1" href="#" title=""><div class="mainmenu menu topmenuimage"><span class="mainmenu tmenuimage" ></span></div></a><a class="tmenu menuhider"  href="#" title=""><span class="mainmenuaspan"></span></a></div></li>';
$("#tmenu_tooltip > div > ul").append(li);
    var Menuu_ = '<!-- Sidebar right -->';
        Menuu_ += '<nav id="sidebarright" class="sidenav">';
        Menuu_ += '<div id="dismiss"><i class="fas fa-times"></i></div>';

        Menuu_ += '<div class="wrapperww">';

        for( rax of $(rows))
        {
            var id_ = $(rax).prop("id");
        if(id_!="mainmenutd_" && id_!="mainmenutd_menu")
            Menuu_ += '<div class="boxww b"><center>'+ $(rax).html()+'</center></div>';


        }
        Menuu_ += '</div>';
        Menuu_ += '</nav>';

        Menuu_ += '<!-- .Sidebar right-->';
      $("#id-top").append(Menuu_);


    $("#sidebarrightbutton").on("click", function() {
            $(".overlay").addClass("active");
            $("body").addClass("noscroll")
        });
        $("#sidebarleftbutton").on("click", function() {
            $("#sidebarleft").addClass("active")
        });
        $("#sidebarrightbutton").on("click", function() {
            $("#sidebarright").addClass("active")
        });

        $("#dismiss, .overlay").on("click", function() {
        $("#sidebarleft").removeClass("active");
        $("#sidebarright").removeClass("active");
        $("body").removeClass("noscroll")
    });
});