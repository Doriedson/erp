<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc gap-10">

    <!-- <div class="box-header">
        Taxa de serviço
    </div> -->

    <div class="flex-responsive gap-10">
        <div class="container flex-8">
            <label class="caption">Descrição</label>
            <div class="addon">
                <!-- BEGIN BLOCK_SOUND_DESCRICAO -->
                <button class="sound_bt_descricao button-field" title="Alterar descrição do som" data-id_som="{id_som}">
                    {descricao}
                </button>
                <!-- END BLOCK_SOUND_DESCRICAO -->
                <!-- BEGIN EXTRA_BLOCK_SOUND_DESCRICAO_FORM -->
                <form method="post" id="frm_sound_descricao" class="fill" data-id_som="{id_som}">
                    <div class="addon">
                        <input
                            type="text"
                            id="sound_descricao"
                            class="fill"
                            required
                            value='{descricao}'
                            placeholder='Descrição'
                            maxlength="100"
                            autofocus>
                    </div>
                </form>
                <!-- END EXTRA_BLOCK_SOUND_DESCRICAO_FORM -->
            </div>
        </div>
        <div class="flex-8">
            <label class="caption">Arquivo de som</label>
            <div class="addon">
                <button type="button" class="sound_bt_sound button-field">{som}</button>
            </div>
        </div>
        <div class="flex gap-10 flex-3">
            <div class="flex-1">
                <label class="caption">Amplificação</label>
                <div class="addon">
                    <!-- BEGIN BLOCK_SOUND_VOLUME -->
                    <button type="button" class="sound_bt_volume button-field" data-id_som="{id_som}">{volume}%</button>
                    <!-- END BLOCK_SOUND_VOLUME -->
                    <!-- BEGIN EXTRA_BLOCK_SOUND_VOLUME_FORM -->
                    <form method="post" id="frm_sound_volume" class="fill" data-id_som="{id_som}">
                        <select id="sound_volume" class="fill" autofocus>{soundvolume_option}</select>
                    </form>
                    <!-- END EXTRA_BLOCK_SOUND_VOLUME_FORM -->
                </div>
            </div>
            <div class="flex flex-ai-fe">
                <button type="button" class="sound_bt_play button-icon button-blue" title="Tocar som" data-id_som="{id_som}">
                    <i class="fa-solid fa-play"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- END BLOCK_PAGE -->