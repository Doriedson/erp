/**
  * Add collaborator.
  */
$(document).on("submit", "#frm_collaborator", async function(event) {

    event.preventDefault();

    let form = $(this);
    let container = $('.collaborator_container');

    let field = form.find(".entity_search");

	let entidade = field.data("sku");

	if (entidade) {

		field.val(field.data('descricao'));

	} else {

		entidade = field.val();
	}

    let data = {
        action: 'collaborator_add',
        value: entidade,
    }

    let response = await Post("collaborator.php", data);

    if (response != null) {

        let content = $(response);

        container.append(content);

        field.val("");

		ContainerFocus(content, true);
	}
});

/**
  * Deletes collaborator.
  */
 $(document).on("click", ".collaborator_bt_del", async function() {

    var button = $(this);

    var container = button.closest('.w-collaborator');

    var id_entidade = container.data("id_entidade");

    data = {
        action: 'collaborator_del',
        value: id_entidade,
    }

    let yes = async function() {

        response = await Post("collaborator.php", data);

        if (response != null) {

            ContainerRemove(container);
        }

        return true;
    }

    let no = async function () {

    }

    MessageBox.Show("Remover acesso de " + button.data('text') + "?", yes, no);
});

/**
  * Changes collaborator access.
  */
 $(document).on("click", ".collaborator_access", async function(event) {

    let button = $(this);

    Disable(button);

    let container = $(this).closest('.w-collaborator-accesslist');

    let data = {
        action: "collaborator_access",
        id_entidade: $(this).closest(".w-collaborator").data("id_entidade"),
        key: $(this).data('key'),
        value: this.checked
    }

    let response = await Post("collaborator.php", data);

    if (response != null) {

        container.html(response);

	} else {

        this.checked = !this.checked;
        Enable(button);
    }
});