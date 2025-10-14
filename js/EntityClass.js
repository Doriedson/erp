export default class Entity {

    // id_entidade
    // cpfcnpj
    // nome
    // telcelular
    // telresidencial
    // telcomercial
    // obs
    // limite
    // datacad
    // credito
    // ativo

    static entitys = [];

    // constructor() {

    // }

    static show() {

        console.log(this.entitys);
        return "teste " + this.entitys.length;
    }
}