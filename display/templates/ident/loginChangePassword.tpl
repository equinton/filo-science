<script src="display/bower_components/zxcvbn/dist/zxcvbn.js">
</script>

<script >
$(document).ready(function() {
	var strength = {
        0: "{t}Le pire{/t} ☹",
        1: "{t}Mauvais{/t} ☹",
        2: "{t}Faible{/t} ☹",
        3: "{t}Bon{/t} ☺",
        4: "{t}Robuste{/t} ☻"
}

var password = $("#pass1");
var meter = $('#password-strength-meter');
var text = $('#password-strength-text');

$("#pass1").on('input', function()
{
    var val = $(this).val();
    var result = zxcvbn(val);
    
    // Update the password strength meter
    $('#password-strength-meter').val(result.score);
   
    // Update the text indicator
    if(val !== "") {
		message = "{t}Force du mot de passe :{/t} " + strength[result.score] +  result.feedback.warning + ". " + result.feedback.suggestions;
    }
    else {
        message = "";
    }
	$("#message").text(message);
});

	$("#formPassword").submit(function (event) {
		var error = false;
		var message = "";
		/*
		 * Verifications
		 */
		 var mdp1 = $("#pass1").val();
		 var mdp2 = $("#pass2").val();
		 if (mdp1 != mdp2) {
		 	error = true;
		 	message = "{t}Les mots de passe ne sont pas identiques{/t}";
		 } else if (verifyLength(mdp1) == false) {
		 	error = true;
		 	message = "{t}Le mot de passe est trop court. Minimum : {/t}" + {$passwordMinLength}+ " {t}caractères{/t}";
		 } else if (verifyComplexity(mdp1) == false) {
		 	error = true;
		 	message = "{t}Le mot de passe n'est pas assez complexe (mixez 3 jeux de caractères parmi les minuscules, majuscules, chiffres et signes de ponctuation){/t}";
		 }
		 $("#message").text(message);
		/*
	 	 * Blocage de l'envoi du formulaire
		 */
		 if (error == true)
			event.preventDefault();
	});

});

</script>

<h2>{t}Modifier le mot de passe{/t}</h2>
<div class="row">
<div class="col-lg-6">
<form id="formPassword" method="post" class="form-horizontal protoform" action="index.php">
<input type="hidden" name="id" value="{$data.id}">
{if $passwordLost == 1}
<input type="hidden" name="token" value="{$token}">
<input type="hidden" name="module" value="passwordlostReinitwrite">
{else}
<input type="hidden" name="module" value="loginChangePasswordExec">
<div class="form-group">
<label for="oldPassword" class="control-label col-md-4">{t}Ancien mot de passe :{/t}</label>
<div class="col-md-8">
<input id="oldPassword" class="form-control" type="password" name="oldPassword" autocomplete="off" autofocus>
</div>
</div>
{/if}
<div class="form-group">
<label for="pass1" class="control-label col-md-4">
{t}Nouveau mot de passe :{/t}
</label>
<div class="col-md-8">
<input type="password" id="pass1" class="form-control" autocomplete="off" name="pass1">
</div>
<div class="col-md-12 center">
	<meter max="4" id="password-strength-meter"></meter>
	<div id="message"></div>
</div>

</div>
<div class="form-group">
<label for="pass2" class="control-label col-md-4">
{t}Répétez le mot de passe :{/t}
</label>
<div class="col-md-8">
<input type="password" id="pass2" name="pass2" class="form-control" autocomplete="off">
</div>
</div>
<div class="form-group center">
      <button type="submit" class="btn btn-primary button-valid">{t}Valider{/t}</button>
</div>
<span id="helpBlock" class="help-block center">{t}Le mot de passe doit avoir une longueur minimale de 10 caractères
Il doit comprendre au minimum 3 types de caractères différents
(minuscule, majuscule, chiffre, ponctuation et autre symboles){/t}</span>
</form>
</div>
</div>
