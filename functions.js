function afficheMenu(obj){
	
	var idMenu     = obj.id;
	var idSousMenu = 'sous' + idMenu;
	var sousMenu   = document.getElementById(idSousMenu);
	
	/*****************************************************/
	/**	si le sous-menu correspondant au menu cliqué    **/
	/** est caché alors on l'affiche, sinon on le cache **/
	/*****************************************************/
	if(sousMenu.style.display == "none"){
		sousMenu.style.display = "block";
	}
	else{
		sousMenu.style.display = "none";
	}
	
}