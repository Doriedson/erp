<!-- BEGIN BLOCK_PAGE -->
<!DOCTYPE HTML>

<html lang="pt-br">

    <head>
        <title>{title}</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="height=device-height, width=device-width, user-scalable=no">
        <!-- <link rel="manifest" href="{manifest}?version={version}"> -->

        <link rel="stylesheet" type="text/css" href="./../css/style.css?version={version}" />
        <link rel="stylesheet" type="text/css" href="./../css/menu.css?version={version}" />
        <link rel="stylesheet" type="text/css" href="./../css/print.css?version={version}" />
        <link rel="stylesheet" type="text/css" href="./../css/login.css?version={version}" />

        <link rel="stylesheet" type="text/css" href="./../vendor/css/jquery-ui.min.css" />
        <link rel="shortcut icon" href="#">

        <!-- <script type="text/javascript" src="./../service_worker.js?version={version}"></script> -->
        <script>
            let version = {version};
        </script>

        <script type="text/javascript" src="./../vendor/js/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="./../vendor/js/jquery-ui.min.js"></script>

        <script type="text/javascript" src="./../js/funcoes.js?version={version}"></script>
        <script type="text/javascript" src="./../js/smart_search.js?version={version}"></script>

        <script type="text/javascript" src="./../js/tab_system.js?version={version}"></script>
    </head>

    <body>
        <div class="flex flex-dc gap-10 padding-b20" style="margin: 0 auto; max-width: 720px;">

            <div class="flex flex-dc " style="padding: 0px;">
                <div class="flex fill pos-rel">
                    <img id="img_digitalmenu_view" class="fill" src="./../assets/digitalmenu_header.png?t={timestamp}">
                </div>

                <div class="flex flex-ai-center flex-jc-center" style="margin-top: -30px; z-index: 0;">

                    <div class="flex flex-ai-center flex-jc-center" style="
                        width: 135px;
                        height: 135px;
                        min-width: 135px;
                        min-height: 135px;
                        border-radius:50%;
                        background-color: white;
                        box-shadow: 0px 5px 5px 0px gray;">
                        <img src="./../assets/digitalmenu_logo.png?t={timestamp}">
                    </div>
                </div>

                <div class="font-size-14 textcenter color-gray-darkest padding-h10 padding-v20">{empresa}</div>

            </div>

            <!-- <div class="font-size-14 textcenter color-gray-darkest padding-h10"><b>{empresa}</b></div> -->

            <div class="w_productsector_container flex flex-dc gap-10">

                <!-- <div class="w_productsector_not_found window card-container tr {hidden}">
                    <div class="font-size-12 textcenter" style="padding: 80px 10px;">
                        Nenhum produto encontrado.
                    </div>
                </div> -->

                <div class="productsector_table flex flex-dc gap-10">

                    {extra_block_product_sector}

                    <!-- BEGIN EXTRA_BLOCK_PRODUCT_SECTOR -->
                    <div class="w_productsector window flex flex-dc gap-10 padding-5">

                        <div class="setor-2 padding-10" style="font-size: 1.8rem;">
                            {produtosetor}
                        </div>

                        <div class="product_container flex flex-dc gap-10">

                            <div class="product_not_found window hidden">
                                <div class="font-size-12" style="padding: 20px 10px;">
                                    Setor sem produto.
                                </div>
                            </div>

                            <div class="product_table card-container table tbody flex flex-dc gap-10">
                                <!-- box-shadow: 0px 3px 5px 0px #C1C1C1; -->
                                {extra_block_product}

                                <!-- BEGIN EXTRA_BLOCK_PRODUCT -->
                                <div class="w-product tr flex gap-10" data-produto="{produto}" data-id_produto="{id_produto}" style="min-height: 120px; padding: 5px;">

                                    <div class="flex flex-dc gap-10 flex-1 padding-l5">

                                        <div class="font-size-12 color-gray-darkest">
                                            {produto}
                                        </div>

                                        <div class="flex-1">
                                            <span class="">{obs}</span>
                                        </div>

                                        <div class="color-blue font-size-12">
                                            R$ {preco_final_formatted} <span class="font-size-075">/{produtounidade}</span>
                                        </div>
                                    </div>

                                    <div style="
                                        background: url('./../pic/{imagem}') no-repeat;
                                        width: 120px;
                                        background-size: contain;
                                        background-position-x: center;
                                        background-position-y: center;">
                                    </div>
                                    <!-- <div class="flex flex-ai-center flex-jc-center">
                                        <img src='./../pic/{imagem}' loading="lazy" style="max-height: 60px;">
                                    </div> -->
                                </div>
                                <!-- END EXTRA_BLOCK_PRODUCT -->
                            </div>

                        </div>
                    </div>
                    <!-- END EXTRA_BLOCK_PRODUCT_SECTOR -->
                </div>
            </div>
        </div>
    </body>
</html>
<!-- END BLOCK_PAGE -->