function insert_style(){

/*$('label').css({
        "font-weight":"bolder"
        }); */

/*$('#ujm_exobundle_interactionqcmtype').children('div').css({
        "border":"1px solid white",
        "margin":"5px",
        "padding":"5px"    
        });                                                             
$('#ujm_exobundle_interactionqcmtype_interaction').children('div').css({
        "border":"1px solid white",
        "margin":"5px",
        "padding":"5px"    
        });
$('#ujm_exobundle_interactionqcmtype_interaction_question').children('div').children('label').css({
        "display":"block",
        "width":"150px",
        "float":"left" 
        });
$('#ujm_exobundle_interactionqcmtype_interaction_question').children('div').css({
        "margin":"5px",
        "padding":"5px"
    });        
$('#ujm_exobundle_interactionqcmtype_interaction').children('div').children('label').css({
        "display":"block",
        "width":"150px",
        "float":"left" 
        });
$('#ujm_exobundle_interactionqcmtype').find('div').css({
        "margin":"5px",
        "padding":"5px"
        });        
$('#ujm_exobundle_interactionqcmtype').children('div').children('label').css({
        "display":"block",
        "width":"200px",
        "float":"left" 
        });*/

$('#ujm_exobundle_interactionqcmtype_interaction').find('div').first().find('label').first().remove();

//deplacer bouton créer une nouvelle catégorie
$('#ujm_exobundle_interactionqcmtype_interaction_question').contents("div:nth-child(2)").append($('#lien_category'));

//supprime colonne ordre                         
$('#ujm_exobundle_interactionqcmtype_shuffle').live('click', function () {
    if ($(this).is(':checked')) { 
        $('#newTable .ligne_choice').each(function(index) {
            $(this).contents('td:nth-child(7)').remove();               
        });
        $('#newTable tr:first').contents('th:nth-child(6)').hide();
    }else{
        $('#newTable .ligne_choice').each(function(index) {
            $('#newTable tr:first').contents('th:nth-child(6)').show();
            $(this).contents('td:nth-child(6)').after('<td id="button_down_click" class="colonne_choice" style="width: 125px; " > <div> <button id="button_up_click" type="button" class="button_up" >Up</button><button type="button" class="button_down" >Down</button> </div>  </td>');
                $('#newTable .button_down').css({
                        "display":"block",
                        "color": "red",
                        "float":"right"
                        });          
                $('#newTable .button_up').css({
                        "display":"block",
                        "color": "red",
                        "float":"left"
                        });                
            $(this).contents('td:nth-child(7)').css({'border': '1px solid #aaaaaa'});                
        });    
    }           
});


//afficher type qcm
$("#ujm_exobundle_interactionqcmtype_typeQCM").change(function() {
    var src = $(this).val();
    if (src == 2) { 
        //changer les reponse attendues en radio button
        $('#newTable .ligne_choice').each(function(index) {
                $(this).contents("td:nth-child(6)").find('input').prop('type','radio');
                $(this).contents("td:nth-child(6)").find('input').removeAttr('id');
                $(this).contents("td:nth-child(6)").find('input').attr('id','reponse_attendue_radio');
        });
    }else{
        var ii=0;
        //changer les reponse attendues en checkbox button
        $('#newTable .ligne_choice').each(function(index) {
                $(this).contents("td:nth-child(6)").find('input').prop('type','checkbox');
                //donner des name différents au inputs
                $(this).contents("td:nth-child(6)").find('input').removeAttr('name');
                $(this).contents("td:nth-child(6)").find('input').attr('name','ujm_exobundle_interactionqcmtype[choices]['+ii+'][rightResponse]');
                $(this).contents("td:nth-child(6)").find('input').removeAttr('id');
                $(this).contents("td:nth-child(6)").find('input').attr('id','ujm_exobundle_interactionqcmtype_choices_'+ii+'_rightResponse');
                ii = ii+1;
        });
    } 

});


//Assign points by response is chiked                        
$('#ujm_exobundle_interactionqcmtype_weightResponse').live('click', function () {
    if ($(this).is(':checked')) { 
        $('#newTable .ligne_choice').each(function(index) {
            $(this).contents("td:nth-child(4)").find('input').removeAttr('disabled');
        });
        $('#ujm_exobundle_interactionqcmtype_scoreRightResponse').attr('disabled','disabled');
        $('#ujm_exobundle_interactionqcmtype_scoreRightResponse').prop('value','');
    }else{
        $('#newTable .ligne_choice').each(function(index) {
            $(this).contents("td:nth-child(4)").find('input').attr('disabled','disabled');
            $(this).contents("td:nth-child(4)").find('input').prop('value','');
        });
        $('#ujm_exobundle_interactionqcmtype_scoreRightResponse').removeAttr('disabled');
    }           
});



$('#reponse_attendue_radio').live('click', function () {    
    if ($(this).is(':checked')) {             
         $('#newTable .ligne_choice').each(function(index) {
                $(this).contents("td:nth-child(6)").find('input').removeAttr('checked');        
        });
    $(this).attr('checked','checked');    
    }
});

// clique boutons down et up - déplacement des lignes du tableau
$('.button_down').live('click', function () {
    var rowToMove = $(this).parents('tr.ligne_choice:first');
    var next = rowToMove.next('tr.ligne_choice');
    if (next.length == 1) {next.after(rowToMove);}

    var i = 0;
    $('#newTable .ligne_choice').each(function(index) {
        $(this).find('label:first').text(i);
	$(this).contents("td:nth-child(3)").find('input').attr('value',i+1); 
        i = i + 1;
    });
});

$('.button_up').live('click', function () {
    var rowToMove = $(this).parents('tr.ligne_choice:first');
    var prev = rowToMove.prev('tr.ligne_choice');
    if (prev.length == 1) {prev.before(rowToMove);}

    var i = 0;
    $('#newTable .ligne_choice').each(function(index) {
        $(this).find('label:first').text(i);
	$(this).contents("td:nth-child(3)").find('input').attr('value',i+1);
        i = i + 1;
    });
});
   
    
}

//css choice
function choice_css()
{   
$('#ujm_exobundle_interactionqcmtype_choices').children('div').each(function(index) {
        $('#newTable').append('<tr class="ligne_choice" >  </tr>');
        $('#newTable .ligne_choice:last').append($(this));       
});

$('#newTable .ligne_choice:last').append('<td class="colonne_choice" >  </td>');
$('#newTable .colonne_choice:last').append($('#newTable .ligne_choice:last').children('div').children('label').first());
$('#newTable .ligne_choice:last').children('div').children('div').children('div').each(function(index) {
        $('#newTable .ligne_choice:last').append('<td class="colonne_choice" >  </td>');
        $('#newTable .colonne_choice:last').append($(this));
});


//ajout colonne ordre
if ($('#ujm_exobundle_interactionqcmtype_shuffle').is(':checked')== false) { 
    $('#newTable .ligne_choice:last').contents('td:nth-child(7)').after('<td id="button_down_click" class="colonne_choice" style="width: 125px; " > <div> <button id="button_up_click" type="button" class="button_up" >Up</button><button type="button" class="button_down" >Down</button> </div>  </td>');
    $('#newTable .button_down').css({
            "display":"block",
            "color": "red",
            "float":"right"
            });          
    $('#newTable .button_up').css({
            "display":"block",
            "color": "red",
            "float":"left"
            });
}                      


$('#newTable .ligne_choice:last').find('div').first().remove()

// css th    
$('#newTable th').css({
        "background-color": "#eee"           
});


//type qcm is chiked                        
if ($('#ujm_exobundle_interactionqcmtype_typeQCM').val() == 2) {             
    $('#newTable .ligne_choice:last').contents("td:nth-child(6)").find('input').prop('type','radio');
    $('#newTable .ligne_choice:last').contents("td:nth-child(6)").find('input').removeAttr('id');
    $('#newTable .ligne_choice:last').contents("td:nth-child(6)").find('input').attr('id','reponse_attendue_radio');
}else{           
    $('#newTable .ligne_choice:last').contents("td:nth-child(6)").find('input').prop('type','checkbox');   
}

//ajout de la derniere colonne pr l'ajout et la supression 
$('#newTable .ligne_choice:last').contents('td:last').after('<td><a href="#" id="delete_choice">supprimer</a> </td> '); 

// clique boutons supprimer lignes du tableau
$('#delete_choice').live('click', function () {
    $(this).parents('tr.ligne_choice:first').remove();
    var i = 0;
    $('#newTable .ligne_choice').each(function(index) {
        $(this).find('label:first').text(i);
        i = i + 1;
    });
    if ($(this).attr("href") == "#") {
        return false;
    }
});


//css td
$('#newTable tr').contents('td').css({'border': '1px solid #aaaaaa'});

//ajouter bouton édition avancée ds colonne réponse
$('#newTable .ligne_choice:last').contents('td:nth-child(2)').children('div').before('<button id="button_editionA" type="button" class="button_editionA" >édition avancée</button>');
    $('#newTable .button_editionA').css({
            "display":"block",
            "color": "red",
            "float":"right"
            });


//ajustement
$('#newTable .ligne_choice:last').contents('td:nth-child(3)').hide();

//Assign points by response is chiked                        

if ($('#ujm_exobundle_interactionqcmtype_weightResponse').is(':checked')) {             
            $('#newTable .ligne_choice:last').contents("td:nth-child(4)").find('input').removeAttr('disabled');           
}else{           
        $('#newTable .ligne_choice:last').contents("td:nth-child(4)").find('input').attr('disabled','disabled');                                     
}

$("td:hidden").find('input').attr('value','1');


}



//css hint
function hint_css()
{

//creer une linge 
$('#ujm_exobundle_interactionqcmtype_interaction_hints').children('div').each(function(index) {
        $('#newTable2').append('<tr class="ligne_choice2" >  </tr>');
        $('#newTable2 .ligne_choice2:last').append($(this));       
});

//deplacer tous les elementts du hint ds une ligne
$('#newTable2 .ligne_choice2:last').append('<td class="colonne_choice2" >  </td>');
$('#newTable2 .colonne_choice2:last').append($('#newTable2 .ligne_choice2:last').children('div').children('label').first());
$('#newTable2 .ligne_choice2:last').children('div').children('div').children('div').each(function(index) {
        $('#newTable2 .ligne_choice2:last').append('<td class="colonne_choice2" >  </td>');
        $('#newTable2 .colonne_choice2:last').append($(this));
});


$('#newTable2 .ligne_choice2:last').find('div').first().remove()

// css th    
$('#newTable2 th').css({
        "background-color": "#eee"           
});


//ajout de la derniere colonne pr l'ajout et la supression 
$('#newTable2 .ligne_choice2:last').contents('td:last').after('<td><a href="#" id="delete_choice2">supprimer</a> </td> ') 


// clique boutons supprimer lignes du tableau
$('#delete_choice2').live('click', function(){
    $(this).parents('tr.ligne_choice2:first').remove();
    //changer lindex de la ligne
    var i2 = 0;
    $('#newTable2 .ligne_choice2').each(function(index) {
        $(this).find('label:first').text(i2);
        i2 = i2 + 1;
    });
    if ($(this).attr("href") == "#") {
        return false;
    }
});

//css td
$('#newTable2 tr').contents('td').css({'border': '1px solid #aaaaaa'});

}


/////////////////////////////////// edit.html.twig

function choice_css_edit(nbResponses)
{   
$('#ujm_exobundle_interactionqcmtype_choices').children('div').each(function(index) {
        $('#newTable').append('<tr class="ligne_choice" >  </tr>');
        $('#newTable .ligne_choice:last').append($(this));       
});

$('#newTable .ligne_choice').each(function(index) {
        $(this).append('<td class="colonne_choice" >  </td>');
        $(this).children('td').first().append($(this).children('div').children('label').first());
});

$('#newTable .ligne_choice').each(function(index) {
    var row = $(this);
    row.children('div').children('div').children('div').each(function(index) {
            row.append('<td class="colonne_choice" >  </td>');
            row.children('td').last().append($(this));
    });
});

//remplacer les input des réponses par des div pr interpreter le html
$('#newTable .ligne_choice').each(function(index) {
    var row = $(this);
    var text = row.contents("td:nth-child(3)").find('textarea').val();   
    row.contents("td:nth-child(3)").find('textarea').hide();
    row.contents("td:nth-child(3)").append('<br /><div id="divReplaceTextarea" style="border:solid 1px red; width:200px; height:110px; padding:5px; overflow:auto; "></div> ');    
    row.contents("td:nth-child(3)").children('div').last().html(text);
    
});


//ajout colonne ordre
if ($('#ujm_exobundle_interactionqcmtype_shuffle').is(':checked')== false) { 
    $('#newTable .ligne_choice').each(function(index) {
        $(this).contents('td:nth-child(7)').after('<td id="button_down_click" class="colonne_choice" style="width: 125px; " > <div> <button id="button_up_click" type="button" class="button_up" >Up</button><button type="button" class="button_down" >Down</button> </div>  </td>');   
    });
    $('#newTable .button_down').css({
            "display":"block",
            "color": "red",
            "float":"right"
            });          
    $('#newTable .button_up').css({
            "display":"block",
            "color": "red",
            "float":"left"
            });
}   

$('#newTable .ligne_choice').each(function(index) {
    $(this).find('div').first().remove();   
});


// css th    
$('#newTable th').css({
        "background-color": "#eee"           
});

//type qcm is chiked                        
if ($('#ujm_exobundle_interactionqcmtype_typeQCM').val() == 2) {
    $('#newTable .ligne_choice').each(function(index) {
        $(this).contents("td:nth-child(6)").find('input').prop('type','radio');
        $(this).contents("td:nth-child(6)").find('input').prop('name','choice');
        $(this).contents("td:nth-child(6)").find('input').removeAttr('id');
        $(this).contents("td:nth-child(6)").find('input').attr('id','reponse_attendue_radio');
        $(this).contents("td:nth-child(6)").find('input').removeAttr('name');
        $(this).contents("td:nth-child(6)").find('input').attr('name','ujm_exobundle_interactionqcmtype[choices]['+index+'][rightResponse]');

    });
}else{
    $('#newTable .ligne_choice').each(function(index) {
        $(this).contents("td:nth-child(6)").find('input').prop('type','checkbox');   
    });
}

//ajout de la derniere colonne pr l'ajout et la supression
$('#newTable .ligne_choice').each(function(index) {
    if(nbResponses == 0)
    {
        $(this).contents('td:last').after('<td><a href="#" id="delete_choice">supprimer</a> </td> ');
    }
    else
    {
        $(this).contents('td:last').after('<td>supprimer</td> ');
    }  
});

// clique boutons supprimer lignes du tableau
$('#delete_choice').live('click', function () {
    $(this).parents('tr.ligne_choice:first').remove();
    //changer lindex de la ligne
    var i = 0;
    $('#newTable .ligne_choice').each(function(index) {
        $(this).find('label:first').text(i);
        i = i + 1;
    });
    if ($(this).attr("href") == "#") {
        return false;
    }
});


//css td
$('#newTable tr').contents('td').css({'border': '1px solid #aaaaaa'});

//ajouter bouton édition avancée ds colonne réponse
$('#newTable .ligne_choice').each(function(index) {
    $(this).contents('td:nth-child(2)').children('div').before('<button id="button_editionA" type="button" class="button_editionA" >édition avancée</button>'); 
});
$('#newTable .button_editionA').css({
        "display":"block",
        "color": "red",
        "float":"right"
        });  
            
$('#newTable .ligne_choice').each(function(index) {  
    $(this).contents("td:nth-child(2)").children('button').first().remove();
});            


//ajustement
$('#newTable .ligne_choice').each(function(index) {
    $(this).contents('td:nth-child(3)').hide(); 
});

//vérifier si le champs Points for correct answer est remplie
if( !$('#ujm_exobundle_interactionqcmtype_scoreRightResponse').val() ) {
        //cocher Assign points by response
        $('#ujm_exobundle_interactionqcmtype_weightResponse').attr('checked', true);
    $('#newTable .ligne_choice').each(function(index) {
        $(this).contents("td:nth-child(4)").find('input').removeAttr('disabled');       
    });
    $('#ujm_exobundle_interactionqcmtype_scoreRightResponse').attr('disabled','disabled');        
}

$('#ujm_exobundle_interactionqcmtype_weightResponse').live('click', function () {
    if ($(this).is(':checked')) { 
    $('#newTable .ligne_choice').each(function(index) {
        $(this).contents("td:nth-child(4)").find('input').removeAttr('disabled');       
    });
    $('#ujm_exobundle_interactionqcmtype_scoreRightResponse').attr('disabled','disabled');
    }else{
    $('#newTable .ligne_choice').each(function(index) {
        $(this).contents("td:nth-child(4)").find('input').attr('disabled','disabled');       
    });
    $('#ujm_exobundle_interactionqcmtype_scoreRightResponse').removeAttr('disabled');   
    }           
});

}

//css hint
function hint_css_edit()
{

$('#ujm_exobundle_interactionqcmtype_interaction_hints').after('<table style="border: 1px solid black;" id="newTable2"><tr> <th>Num indice</th> <th>Indice</th> <th>penalité</th> <th>------</th> </tr></table>');    

//creer une ligne 
$('#ujm_exobundle_interactionqcmtype_interaction_hints').children('div').each(function(index) {
        $('#newTable2').append('<tr class="ligne_choice2" >  </tr>');
        $('#newTable2 .ligne_choice2:last').append($(this));       
});

//deplacer tous les elements du hint ds une ligne

$('#newTable2 .ligne_choice2').each(function(index) {
        $(this).append('<td class="colonne_choice2" >  </td>');
        $(this).children('td').first().append($(this).children('div').children('label').first());
});

$('#newTable2 .ligne_choice2').each(function(index) {
    var row = $(this);
    row.children('div').children('div').children('div').each(function(index) {
            row.append('<td class="colonne_choice2" >  </td>');
            row.children('td').last().append($(this));
    });
});


$('#newTable2 .ligne_choice2').each(function(index) {
    $(this).find('div').first().remove();   
});


// css th    
$('#newTable2 th').css({
        "background-color": "#eee"           
});

//ajout de la derniere colonne pr l'ajout et la supression 
$('#newTable2 .ligne_choice2').each(function(index) {
    $(this).contents('td:last').after('<td><a href="#" id="delete_choice2">supprimer</a> </td> ');  
});

// clique boutons supprimer lignes du tableau
$('#delete_choice2').live('click', function(){
    $(this).parents('tr.ligne_choice2:first').remove();
    //changer lindex de la ligne
    var i2 = 0;
    $('#newTable2 .ligne_choice2').each(function(index) {
        $(this).find('label:first').text(i2);
        i2 = i2 + 1;
    });
    if ($(this).attr("href") == "#") {
        return false;
    }
});

//css td
$('#newTable2 tr').contents('td').css({'border': '1px solid #aaaaaa'});

}

//check form 
function check_form(nbr_choices, answer_coched, label_empty, point_answers, point_answer, invite_question) {

//vérifier qu'il y a au moins deux choix       
if (($('#newTable .ligne_choice').length) < 2) {               
    alert(nbr_choices);
    return false;
}
else{
	//vérifier qu'il y a des réponse attendue   
	var nbr_rep_coched = 0;
	$('#newTable .ligne_choice').each(function(index) {
		if ($(this).contents("td:nth-child(6)").find('input').is(':checked')) {               
			nbr_rep_coched = nbr_rep_coched + 1;
		}
	});        
	if (nbr_rep_coched == 0) {               
		alert(answer_coched);
		return false;
	}
	else{
		//vérifier les points des reponses
		if ($('#ujm_exobundle_interactionqcmtype_scoreRightResponse').val() == 0) {
			if ($('#ujm_exobundle_interactionqcmtype_weightResponse').is(':checked')) { 
				var nbr_point = 0;
				$('#newTable .ligne_choice').each(function(index) {
					nbr_point = nbr_point + $(this).contents("td:nth-child(4)").find('input').val();
				});        

				if (nbr_point <= 0) {    
					alert(point_answers);
					nbr_point = 0;
					return false;
				}
			}
			else{
				alert(point_answer);				
				return false;
			}
		}
		else{
			//vérifier que l'invite de la question est rempli      
			if ($('#ujm_exobundle_interactionqcmtype_interaction_invite').val() == 0) {               
				alert(invite_question);
				return false;
			}
			else
			{
				var ii = 0;
				$('#newTable .ligne_choice').each(function(index) {   
					$(this).contents("td:nth-child(3)").find('input').attr('value',ii+1);
					ii = ii + 1;
				});
				return true;	
			}	
		}	
	}
}

}


//add choices
function add_form_choice(multiple_response, unique_response, add, Response_number, Response, point, comment, expected_response, order, source_image_add) 
{

$('#ujm_exobundle_interactionqcmtype_typeQCM')
    .find('option')
    .remove()
    .end()
    .append('<option selected="selected" value="1">'+multiple_response+'</option><option value="2">'+unique_response+'</option>')

$('#ujm_exobundle_interactionqcmtype_choices').before('<a href="#" id="add_choice"><img src="' + source_image_add + '">'+add+'</a>');
     
$('#ujm_exobundle_interactionqcmtype_choices').after('<table style="border: 1px solid black;" id="newTable"><tr> <th>'+Response_number+'</th> <th>'+Response+'</th> <th>'+point+'</th> <th>'+comment+'</th> <th>'+expected_response+'</th> <th>'+order+'</th> <th>------</th> <th>------</th> </tr></table>');
$('#add_choice').css({
        "display":"block",
        "color": "green",
        "float":"right"
        });
var $container = $('#ujm_exobundle_interactionqcmtype_choices');
function add_choice() {
    index = $('#newTable .ligne_choice').length;

    $container.append(
        $($container.attr('data-prototype').replace(/__name__/g, index))
    );
    choice_css();
}

if($('#newTable').children('.ligne_choice').length == 0) {
    add_choice();
    add_choice();      
} 

$('#add_choice').click(function() {
    add_choice();
    if ($(this).attr("href") == "#") {
        return false;
    }
});

}

function add_form_hint(add_h, hint_number, hint, Penalty, source_image_add, category_new_pop) 
{

$('#ujm_exobundle_interactionqcmtype_interaction_hints').before('<a href="#" id="add_hint"><img src="' + source_image_add + '">'+add_h+'</a>');
$('#add_hint').css({
        "display":"block",
        "color": "green",
        "float":"bottom"
        });
var $container = $('#ujm_exobundle_interactionqcmtype_interaction_hints');

function add_hint() {

    if ($("#newTable2").length){
        index2 = $("#newTable2 .ligne_choice2").length;
    }
    else{
        index2 = 0;
        $('#ujm_exobundle_interactionqcmtype_interaction_hints').after('<table style="border: 1px solid black;" id="newTable2"><tr> <th>'+hint_number+'</th> <th>'+hint+'</th> <th>'+Penalty+'</th> <th>------</th> </tr></table>');
    }

    $container.append(
        $($container.attr('data-prototype').replace(/__name__/g, index2))
    );
    hint_css();
}
  
$('#add_hint').click(function() {
    add_hint();
    if ($(this).attr("href") == "#") {
        return false;
    }
});

$('#lien_category').click(function() {

    $.ajax({
    type: "POST",
    url: category_new_pop,
    cache: false,
    success: function(data){
    category_pop(data);
    }
    });
});

}

		
function add_form_choice_edit(multiple_response, unique_response, add, Response_number, Response, point, comment, expected_response, order, source_image_add, nbResponses, typeQCM) 
{

$('#ujm_exobundle_interactionqcmtype_typeQCM')
    .find('option')
    .remove()
    .end()
    
if (typeQCM == 1){
    $('#ujm_exobundle_interactionqcmtype_typeQCM').append('<option selected="selected" value="1">'+multiple_response+'</option><option value="2">'+unique_response+'</option>')
}
else{
    $('#ujm_exobundle_interactionqcmtype_typeQCM').append('<option value="1">'+multiple_response+'</option><option selected="selected" value="2">'+unique_response+'</option>')
}
	
if (nbResponses == 0){
    $('#ujm_exobundle_interactionqcmtype_choices').before('<a href="#" id="add_choice"><img src="' + source_image_add + '">'+add+'</a>');
}


$('#ujm_exobundle_interactionqcmtype_choices').after('<table style="border: 1px solid black;" id="newTable"><tr> <th>'+Response_number+'</th> <th>'+Response+'</th> <th>'+point+'</th> <th>'+comment+'</th> <th>'+expected_response+'</th> <th>'+order+'</th> <th>------</th> <th>------</th> </tr></table>');
$('#add_choice').css({
        "display":"block",
        "color": "green",
        "float":"right"
        });

var $container = $('#ujm_exobundle_interactionqcmtype_choices');

function add_choice() {
    index = $('#newTable .ligne_choice').length;

    $container.append(
        $($container.attr('data-prototype').replace(/__name__/g, index))
    );
    choice_css();
}

$('#add_choice').click(function() {
    add_choice();
if ($(this).attr("href") == "#") {
    return false;
}
});

}

function add_form_hint_edit(add_h, source_image_add, category_new_pop) 
{

$('#ujm_exobundle_interactionqcmtype_interaction_hints').before('<a href="#" id="add_hint"><img src="' + source_image_add + '">'+add_h+'</a>');
$('#add_hint').css({
        "display":"block",
        "color": "green",
        "float":"bottom"
        });
var $container = $('#ujm_exobundle_interactionqcmtype_interaction_hints');
function add_hint() {

        if ($("#newTable2").length){
            index = $("#newTable2 .ligne_choice2").length;
        }
        else{
            index = 0;
        }       
        $container.append(
            $($container.attr('data-prototype').replace(/__name__/g, index))
        );
        hint_css();
}


$('#add_hint').click(function() {
    add_hint();

if ($(this).attr("href") == "#") {
    return false;
}
});	

$('#lien_category').click(function() {

                $.ajax({
                type: "POST",                   
                url: category_new_pop,
                cache: false,                   
                success: function(data){                       
                    category_pop(data);                            
                }                   
                });                     

});	

}
