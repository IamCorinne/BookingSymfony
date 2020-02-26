$('#add-image').click(function()
{
     //récupérer le n° des futurs champs (sont en boucles) donc la valeur de l'index 
      const index=+$('#widget-count').val();
    //récupérer le prototype des entrées le num index
    const tmpl= $('#annonce_images').data('prototype').replace(/_name_/g,index);
    //injecter le code dans la div (.append perlet de rajouter)
    $('#annonce_images').append(tmpl);
    //on ajoute 1 pour le comptage
    $('#widget-count').val(index+1);
    //suppression des boutons
    deleteButtons();
});


//personnalisation de la fonction pour éviter les bugs de suppression ou retour en arrière, on va comparer le compteur et le nombre de div affichées
function updateCounter()
{
     const count=+$('#annonce_images div.form-group').length;
    //on met à jour la valeur de widget
    $('#widget-count').val(count);
}
//pour supprimer un champs inséré
function deleteButtons()
{
        $('button[data-action="delete"]').click(function () {
            //on cible les données contenant target
            const target = this.dataset.target;
            //on supprime
            $(target).remove();
        });
}

 //on appelle la fonction
 updateCounter();
 deleteButtons();

