import { createI18n,useI18n } from 'vue-i18n';

const messages = {
    pt: {
        apps: {
            pdvName: 'Ponto de Venda',
            configName: 'Configurações',
            employeeName: 'Funcionário',
            purchase: 'Compra',
            stock: 'Gestão de Estoque',
            invoicing: 'Faturamento'
        },

        month: {
            January: "Janeiro",
            February: "Fevereiro",
            March: "Março",
            April: "Abril",
            May: "Maio",
            June: "Junho",
            July: "Julho",
            August: "Agosto",
            September: "Setembro",
            October: "Outubro",
            November: "Novembro",
            December: "Dezembro"
        },

        words: {
            startDate: 'Data de inicio',
            endDate: 'Data de fin',
            department: 'Departamento',
            connected: 'conectado',
            installed: 'instalados',
            module: 'modulos',
            by: 'por',
            create: 'Criar',
            company: 'Empresa',
            day: "dia's",
            month: "mêse's",
            hour: "hora's",
            start: 'Iniciar',
            close: 'Fechar',
            goBack: 'Voltar',
            next: 'Avançar',
            conclude: 'Concluir',
            active: 'ativo',
            end: 'Terminar',
            enter: 'Entrar',
            one: 'Uma',
            open: 'Abrir',
            continue: 'Continuar',
            see: 'Ver',
            order: 'Encomenda',
            sale: 'Venda',
            new: 'Novo',
            report: 'Relatório',
            article: 'Artigo',
            operation: 'Operações',
            evaluation: 'Avaliação',
            list: 'Lista',
            entry: 'Entrada',
            output: 'Saída',
            price: 'Preço',
            action: 'Ação',
            section: 'Seção',
            number: 'Número',
            state: 'Estado',
            result: 'Resultado',
            client: 'Cliente',
            employee: 'Funcionário',
            total: 'Total',
            group: 'Grupo',
            filter: 'Filtrar',
            change: 'Trocar',
            cash: 'Dinheiro',
            quantity: 'Quantidade',
            requested: 'Solicitada',
            canceled: 'Canceladas',
            completed: 'Concluída',
            search: 'Pesquisar...',
            name: 'Nome',
            formulary: 'Formulário (s)',
            panel: 'Painel',
            movement: 'Movimento (s)',
            subject: 'Assunto',
            date: 'Data',
            added: 'Adicionado',
            expenses: 'Gastos',
            print: 'Imprimir',
            cost: 'Custo',
            profit: 'Lucro',
            select: 'Selecionar',
            category: 'Categoria',
            provider: 'Fornecedor',
            notice: 'Informação',
            code: 'Código',
            barre: 'Código de Barras',
            save: 'Salvar',
            opened: 'Aberto',
            opening: 'Abertura',
            transfer: 'Transferência',
            card: 'Cartão',
            difference: 'Diferença',
            informed: 'Informado',
            payment: 'Pagamento',
            continue: 'Continuar',
            toPay: 'a pagar',
            goOut: 'Sair',
            cancel: 'Cancelar',
            credit: 'Crédito',
            discount: 'Desconto',
            tax: 'Imposto',
            permission: "Permissão\(s)",
            due: 'Vencimento',
            final: 'Final',
            annul: 'Anular',
            user: 'Usuário',
            after: 'Depois',
            before: 'Antes',
            admin: 'Administrador',
            paid: 'Pago',
            pay: 'Pagar',
            surname: 'Sobrenome',
            phone: 'Telefone',
            country: 'País',
            city: 'Cidade',
            road: 'Rua',
            confirm: 'Confirmar',
            delete: 'Eliminar',
            archive: 'Arquivar',
            license: 'licensa',
            purchase: 'Compra',
            merchandise: 'Mercadoria',
            department: 'Departamento',
            store: 'Armazém',
            responsible: 'Responsável',
            of: 'de',
            method: 'Metodo',
            amount: 'montante',
            subTotal: 'Sub total',
            calendar: 'calendario',
            reported: 'Relatado',
            related: 'relacionado',
            last: 'ultima',
            inventory: 'inventário',
            join: 'Aderir',
            the: 'a',
            validate: 'validar',
            missing: 'em falta',
            edit: 'editar',
            show: 'mostrar',
            box: 'caixa',
            resetPassword: 'redefinir a senha',
            lessEqual: 'menor egual',
            a: 'um',
            or: 'ou',
            type: 'tipo',
            sessions: 'Sesão',
            time: 'Cronômetro',
            activate: 'ativar',
            message: 'mensagem',
            manager: 'Gerente',
            neighborhood: 'Bairo',
            houseNumber: 'Numero de casa',
            activityType: "Tipo de atividades",
            currency: 'Moeda',
            localisation: 'Localisação',
            level: "Nivel de acesso",
            image: 'Imagem',
            security: 'Segurança',
            definition: 'Definições',
            update: 'Atualizar',
            gender: 'Genero',
            birthday: 'Aniversario',
            greaterEqual: 'maior egual',
            userConfig: {
                print: 'Imprimir informação completa no PDV',
                reprint: 'Reimprimir recibo PDV',
                stockMovement: 'Pode fazer entrada de stock',
                accesPrice: 'Acesso aos preços',
                language: 'Idioma'
            },
            catalog: 'Catalogo',
            trainer: 'Trenadore',
            activity: 'Atividades',
            salary: 'Salario',
            title: 'Cargo',
            reportSalary:'Folha de salario',
            employeeObj:{
                times: 'Horario de trabalho',
                dialyExpen: 'Dispesas diarias',
                user: 'Usuario relacionado'
            },
            stockObj: {
                qtdRequested: 'Total de quantidade solicitada',
                qtdCanseled: 'otal de quantidade devolvidos',
                qtdConq: 'Total de quantidade comprados',
                prodMxProfit: 'Produto com mais lucro',
                prodMinProfit: 'Produto com menos lucro',
                prodMxSale: 'Produto mais vendido',
                prodMinSale: 'Produto menos vendido',
                storeDestination: 'Armagen de destino',
                dateTransfer: 'Data de transferencia',
                itemsSold: 'Produtos vendidos',
                qtdAvailable: 'Quantidades disponiveis',
                qtdSold: 'Quantidade vendida',
                hasDiscount: 'Este artigo não tem disconto',
                stockUnevailable: 'Estoque indisponível',
                stockAvailable: 'Estoque disponível',
                itemDiscount: 'Itens com desconto'
            },
        },
        message: {
            notData: "Nenhum {colum} encontrado com este {value} .",
            serverError: "Erro do servidor",
            emptyInput: "O campo {name} não pode ser vazio.",
            outStock: 'Este produto não tem stock suficiente',
            userWithOutAccess: 'Usuario sem acesso.'
        },
        permissions:{
            Edit: 'Editar',
            Create: 'Criar',
            Archive: 'Arquivar',
            Delete: 'Apagar',
            Active: 'Ativar',
            Editcostprice: 'Edita preço de compra',
            Stockentry: 'Faz entrada de stock',
            Outofstock: 'Faz saida de stock',
            Editcostsale: 'Edita preço de venda',
            Editsession: 'Edita sessoes de caixa',
            Canmovemoney: 'Pode fazer muvemento de dinheiro',
            ReprintPointofSalereceipt: 'Pode reimprimir fatura',
            Printcompleteinformationcompany: 'Pode imprimir as informações completa da empresa',
            Caneditpriceofproduct: 'Pode editar preço no pdv',
            Closesession: 'Fecher sessoes',
            Cancelinvoice: 'Pode anular fatura',
            Opensession: 'Pode abrir sessão',
            Print: "Imprimir",
            Cancel: "Anular",
        },
        pdv: {
            openingDate: 'Data de Abertura',
            closeDate: 'Data de Fechamento',
            openingBy: 'Aberto por',
            formedMoney: 'Valor enformado'
        },
        phrases: {
            closeControl: "Fechar controle",
            typeProductName: 'Digite o nome do artigo',
            priceProduct: 'O preço do artigo',
            costPrice: 'Preço de compra',
            salePrice: 'Preço de venda',
            totalCost: 'Custo total',
            totalSale: 'Vendas totais',
            unitProfit: 'Lucro unitário',
            totalProfit: 'Lucro total',
            openingDate: 'Data de Abertura',
            closeDate: 'Data de Fechamento',
            openingBy: 'Aberto por',
            feature: 'Funcionalidade',
            userExperience: 'Experiência do usuário',
            securityUpdate: 'Atualização de segurança',
            opSessionContr: 'Abertura de controlo da seção',
            amountOpining: 'Valor de abertura',
            nifCompany: "Numero de identificação fiscal",
            companyName: "Nome da empresa",
            userInfo: 'Informações do usuário',
            confirmPassword: 'Confirme sua senha',
            orderDate: 'Data do pedido',
            dueDate: 'Data de vencimento',
            restToPay: 'Resto a pagar',
            visitSite: 'Visita nosso site',
            contactUs: 'Contactar-nos',
        },
        login: {
            title: 'Identifique-se',
            password: 'Senha',
            info: [
                {
                    title: 'Potencialize Suas Vendas com Nosso Software de Gestão de PDV Avançado.',
                    description: 'Eleve a eficiência das suas vendas com o Módulo de PDV de última geração, integrado ao nosso completo software de gestão.'
                },
                {
                    title: 'Controle Financeiro Simplificado com Nosso Software de Gestão de Faturas.',
                    description: 'Simplifique a emissão de faturas e mantenha suas finanças em ordem com o Módulo de Faturamento do nosso abrangente software de gestão.'
                },
                {
                    title: 'Otimize Suas Compras com Nosso Software de Gestão de Processos de Compra.',
                    description: 'Aprimore seu processo de compra com eficiência utilizando o Módulo de Compras do nosso software de gestão, integrando todas as etapas de maneira simplificada.'
                },
                {
                    title: 'Explore Novas Possibilidades com a Última Funcionalidade do Nosso Software de Gestão.',
                    description: 'Maximize a experiência do usuário com nossa mais recente funcionalidade, integrada ao nosso avançado software de gestão, proporcionando inovação e eficiência.'
                },
                {
                    title: 'Otimize o Controle Total do Seu Estoque com o Nosso Módulo de Gerenciamento Integrado.',
                    description: 'Mantenha seus produtos sob controle e reduza riscos de falta ou excesso com o Módulo de Gerenciamento de Stock e Inventário do nosso avançado software de gestão. Tenha visibilidade completa, atualizações em tempo real e ferramentas intuitivas para aprimorar a eficiência operacional da sua empresa.'
                }
            ]
        }
    },

    fr: {
        apps: {
            pdvName: 'Point de Vente',
            configName: 'Définitions',
            employeeName: 'Employée',
            purchase: 'Achat',
            stock: 'Gestion de stock',
            invoicing: 'Facturation'
        },

        month: {
            January: "Janvier",
            February: "Février",
            March: "Mars",
            April: "Avril",
            May: "Mai",
            June: "Juin",
            July: "Juillet",
            August: "Août",
            September: "Septembre",
            October: "Octobre",
            November: "Novembre",
            December: "Décembre"
        },

        words: {
            startDate: 'Date de debut ',
            endDate: 'Date de fin',
            method: 'Méthode',
            department: 'Département',
            connected: 'connecté',
            installed: 'installée',
            module: 'module',
            by: 'par',
            create: 'Créer',
            formulary: 'Formulair',
            company: 'Entreprise',
            start: 'Démarrer',
            close: 'Fermer',
            end: 'Fin',
            enter: 'Entrer',
            one: 'Un',
            open: 'Ouvrir',
            continue: 'Continuer',
            see: 'Voir',
            order: 'Commande',
            sale: 'Vente',
            new: 'Nouveau',
            report: 'Rapport',
            article: 'Article',
            operation: 'Opérations',
            evaluation: 'Évaluation',
            list: 'Liste',
            entry: 'Entrée',
            output: 'Sortie',
            price: 'Prix',
            action: 'Action',
            goBack: 'Retourner',
            next: 'Avance',
            conclude: 'Conclure',
            section: 'Section',
            number: 'Numéro',
            state: 'État',
            result: 'Résultat',
            client: 'Client',
            employee: 'Employé',
            greaterEqual: 'supérieur égal',
            lessEqual: 'moins égal',
            total: 'Total',
            toPay: 'a payé',
            group: 'Groupe',
            filter: 'Filtrer',
            change: 'Changer',
            subTotal: 'Sus-total',
            cash: 'Espèces',
            quantity: 'Quantité',
            requested: 'Demandée',
            canceled: 'Annulées',
            completed: 'Terminée',
            search: 'Rechercher...',
            name: 'Nom',
            panel: 'Panneau',
            movement: 'Mouvement',
            subject: 'Sujet',
            date: 'Date',
            added: 'Ajouté',
            expenses: 'Dépenses',
            print: 'Imprimer',
            activate: 'activé',
            cost: 'Coût',
            profit: 'Profit',
            select: 'Sélectionner',
            category: 'Catégorie',
            provider: 'Fournisseur',
            notice: 'Avis',
            code: 'Code',
            barre: 'Code-barres',
            save: 'Enregistrer',
            opened: 'Ouvert',
            opening: 'Ouverture',
            transfer: 'Transfert',
            card: 'Carte',
            difference: 'Différence',
            informed: 'Informé',
            payment: 'Paiement',
            continue: 'Continuer',
            the: 'le',
            join: 'Rejoins',
            goOut: 'Sortir',
            cancel: 'Annuler',
            credit: 'Crédit',
            discount: 'Remise',
            tax: 'Taxe',
            due: 'Échéance',
            time: 'Minuteur',
            active: 'actif',
            final: 'Final',
            annul: 'Annuler',
            day : "jour's",
            month : "mois",
            hour : "heure",
            user: 'Utilisateur',
            after: 'Après',
            before: 'Avant',
            admin: 'Administrateur',
            paid: 'Payé',
            pay: 'Payer',
            surname: 'Nom de famille',
            phone: 'Téléphone',
            reported: 'Rapporté',
            country: 'Pays',
            city: 'Ville',
            road: 'Rue',
            delete: 'Supprimer',
            archive: 'Archiver',
            permission: "Autorisation\(s)",
            purchase: 'Achat',
            merchandise: 'Marchandise',
            department: 'Département',
            store: 'Magasin',
            responsible: 'Responsable',
            of: 'de',
            update: 'Metre à jour',
            amount: 'quantia',
            calendar: 'Calendrier',
            related: 'en rapport',
            last: 'dernier',
            inventory: 'inventaire',
            validate: 'valider',
            missing: 'manquante',
            edit: 'modifier',
            show: 'montre',
            box: 'boîte',
            or: 'ou',
            type: 'type',
            sessions: 'Session',
            resetPassword: 'Réinitialiser le mot de passe',
            manager: 'Directeur',
            neighborhood: 'Quartier',
            houseNumber: 'Numéro de maison',
            activityType: "type d'activité",
            a: 'un',
            currency: 'Devise',
            localisation: 'Emplacement',
            license: 'license',
            level: "niveau d'accès",
            image: 'Image',
            security: 'Sécurité',
            definition: 'Définitions',
            gender: 'Genre',
            birthday: 'Anniversaire',
            message: 'message',
            confirm: 'Confirmer',
            userConfig: {
                print: 'Imprimer des informations complètes sur le PDV',
                reprint: 'Réimprimer le reçu du PDV',
                stockMovement: 'Peut effectuer une entrée de stock',
                accesPrice: 'Accès aux prix',
                language: 'Langue'
            },
            catalog: 'Catalogue',
            reportSalary:'Feuille de paiement',
            title: 'Poste',
            trainer: 'Entraîneur',
            activity: 'Activités',
            salary: 'Salaire',
            employeeObj:{
                times: 'Horaire de travail',
                dialyExpen: 'Dépenses quotidiennes',
                user: 'Utilisateur associé'
            },
            stockObj: {
                closeControl: "Fermer le contrôle",
                qtdRequested: 'Quantité totale demandée',
                qtdCanseled: 'Quantité totale annulée',
                qtdConq: 'Quantité totale achetée',
                prodMxProfit: 'Produit le plus rentable',
                prodMinProfit: 'Produit le moins rentable',
                prodMxSale: 'Produit le plus vendu',
                prodMinSale: 'Produit le moins vendu',
                storeDestination: 'Stockage de destination',
                dateTransfer: 'Date de transfert',
                itemsSold: 'Produits vendus',
                qtdAvailable: 'Quantités disponibles',
                qtdSold: 'Quantité vendue',
                hasDiscount: "Cet article n'est pas réduit",
                stockUnevailable: 'Stock non disponible',
                stockAvailable: 'Stock disponible',
                itemDiscount: 'Articles en promotion'
            }

        },
        message: {
            notData: "Aucun {colum} trouvé avec ce {value} .",
            serverError: "Une erreur du serveur s'est produite",
            emptyInput: "Le champ {name} ne peut pas être vide.",
            outStock : "Ce produit n'a pas assez de stock",
            userWithOutAccess: 'Utilisateur sans accès'
        },
        permissions:{
            Edit : 'Modifier',
            Create: 'Créer',
            Archive: 'Archives',
            Print: "Imprimé",
            Cancel: "Anulé",
            Delete: 'Supprimer',
            Active : 'Activer',
            Editcostprice: "Modifier le prix d'achat",
            Stockentry: 'Effectuer une entrée de stock',
            Outofstock: 'En rupture de stock',
            Editcostsale: 'Modifier le prix de vente',
            Editsession: 'Modifier les sessions cash',
            Canmovemoney:"Peut movementé de l'argent",
            ReprintPointofSalereceipt:'Peut réimprimer la facture',
            Printcompleteinformationcompany: "Vous pouvez imprimer les informations complètes de l'entreprise",
            Caneditpriceofproduct: 'Vous pouvez modifier le prix au point de vente',
            Closesession: 'Fermer les sessions',
            Cancelinvoice: 'Vous pouvez annuler la facture',
            Opensession: 'Vous pouvez ouvrir une session',
        },
        pdv: {
            openingDate: 'Date d\'ouverture',
            closeDate: 'Date de fermeture',
            openingBy: 'Ouvert par',
            formedMoney: 'Valeur saisie'
        },
        phrases: {
            typeProductName: 'Saisissez le nom de l\'article',
            priceProduct: 'Le prix de l\'article',
            costPrice: 'Prix d\'achat',
            salePrice: 'Prix de vente',
            totalCost: 'Coût total',
            totalSale: 'Ventes totales',
            unitProfit: 'Profit unitaire',
            totalProfit: 'Profit total',
            openingDate: 'Date d\'ouverture',
            closeDate: 'Date de fermeture',
            openingBy: 'Ouvert par',
            feature: 'Fonctionnalité',
            userExperience: 'Expérience utilisateur',
            securityUpdate: 'Mise à jour de sécurité',
            opSessionContr: 'Ouverture de contrôle de la session',
            amountOpining: "Montant d'ouverture",
            nifCompany: "Numéro d'identification fiscale",
            companyName: "Nom de l'entreprise",
            userInfo: "Informations d'utilisateur",
            confirmPassword: 'Confirmer votre mot de passe',
            orderDate: 'Date de commande',
            dueDate: "Date d'échéance",
            restToPay: 'Reste à payer',
            visitSite: 'Visitez notre site Web',
            contactUs: 'Contactez-nous'
        },
        login: {
            title: 'Identifiez-vous',
            password: 'Mot de passe',
            info: [
                {
                    title:"Boostez vos ventes avec notre logiciel avancé de gestion de point de vente.",
                    description: "Augmentez l'efficacité de vos ventes avec le module POS de dernière génération, intégré à notre logiciel de gestion complet."
                },
                {
                    title: 'Contrôle financier simplifié grâce à notre logiciel de gestion des factures.',
                    description: 'Simplifiez la facturation et gardez vos finances en ordre grâce au module de facturation de notre logiciel de gestion complet.'
                },
                {
                    title: "Optimisez vos achats avec notre logiciel de gestion des processus d'achat.",
                    description: "Améliorez efficacement votre processus d'achats grâce au Module Achats de notre logiciel de gestion, intégrant toutes les étapes de manière simplifiée."
                },
                {
                    title: 'Explorez de nouvelles possibilités avec la dernière fonctionnalité de notre logiciel de gestion.',
                    description : "Maximisez l'expérience utilisateur avec nos dernières fonctionnalités, intégrées à notre logiciel de gestion avancé, offrant innovation et efficacité."
                },
                {
                    title: 'Optimisez le contrôle total de votre inventaire grâce à notre module de gestion intégré.',
                    description : "Gardez vos produits sous contrôle et réduisez les risques de pénurie ou d'excédent avec le module de gestion des stocks et des stocks de notre logiciel de gestion avancé. Bénéficiez d'une visibilité complète, de mises à jour en temps réel et d'outils intuitifs pour améliorer l'efficacité opérationnelle de votre entreprise"
                }
            ]
        }
    },


    en: {
        apps: {
            pdvName: 'Point of Sale',
            configName: 'Settings',
            employeeName: 'Employee',
            purchase: 'Purchase',
            stock: 'Stock Management',
            invoicing: 'Invoicing',
        },
        month: {
            January: "January",
            February: "February",
            March: "March",
            April: "April",
            May: "May",
            June: "June",
            July: "July",
            August: "August",
            September: "September",
            October: "October",
            November: "November",
            December: "December"
        },
        words: {
            startDate: 'Start date ',
            endDate: 'End date',
            subTotal: 'Sub total',
            department: 'Departmente',
            connected: 'connected',
            installed: 'installed',
            module: 'module',
            by: 'by',
            create: 'Create',
            company: 'Company',
            start: 'Start',
            close: 'Close',
            end: 'End',
            enter: 'Enter',
            one: 'One',
            open: 'Open',
            continue: 'Continue',
            see: 'See',
            order: 'Order',
            sale: 'Sale',
            formulary: 'Formulary',
            new: 'New',
            report: 'Report',
            article: 'Article',
            operation: 'Operations',
            evaluation: 'Evaluation',
            list: 'List',
            entry: 'Entry',
            output: 'Output',
            price: 'Price',
            action: 'Action',
            section: 'Section',
            number: 'Number',
            method: 'Method',
            greaterEqual: 'greater equal',
            lessEqual: 'less equal',
            state: 'State',
            result: 'Result',
            client: 'Client',
            employee: 'Employee',
            total: 'Total',
            toPay: 'to pay',
            join: 'Join',
            group: 'Group',
            filter: 'Filter',
            change: 'Change',
            cash: 'Cash',
            quantity: 'Quantity',
            requested: 'Requested',
            canceled: 'Canceled',
            completed: 'Completed',
            search: 'Search...',
            name: 'Name',
            panel: 'Panel',
            movement: 'Movement',
            activate: 'activate',
            subject: 'Subject',
            license: 'license',
            date: 'Date',
            goBack: 'Go back',
            next: 'Next',
            conclude: 'Conclude',
            time: 'Time',
            added: 'Added',
            expenses: 'Expenses',
            print: 'Print',
            cost: 'Cost',
            profit: 'Profit',
            select: 'Select',
            category: 'Category',
            provider: 'Provider',
            active: 'active',
            notice: 'Notice',
            code: 'Code',
            barre: 'Bar',
            save: 'Save',
            opened: 'Opened',
            opening: 'Opening',
            transfer: 'Transfer',
            card: 'Card',
            difference: 'Difference',
            informed: 'Informed',
            payment: 'Payment',
            continue: 'Continue',
            goOut: 'Go Out',
            cancel: 'Cancel',
            credit: 'Credit',
            discount: 'Discount',
            tax: 'Tax',
            due: 'Due',
            final: 'Final',
            annul: 'Annul',
            user: 'User',
            after: 'After',
            before: 'Before',
            admin: 'Admin',
            paid: 'Paid',
            pay: 'Pay',
            surname: 'Surname',
            phone: 'Phone',
            country: 'Country',
            city: 'City',
            road: 'Road',
            delete: 'Delete',
            archive: 'Archive',
            purchase: 'Purchase',
            merchandise: 'Merchandise',
            department: 'Department',
            store: 'Store',
            responsible: 'Responsible',
            box: 'Box',
            sessions: 'Sessions',
            definition: 'Definitions',
            reported: 'Reported',
            permission: "Permission\(s)",
            message: 'message',
            day: "day's",
            month: "month's",
            hour: "hour's",
            of: 'of',
            tape: 'Tape',
            type: 'type',
            the: 'the',
            theArticle: 'Article',
            or: 'or',
            a: 'A',
            A: 'A',
            update: 'Update',
            reason: 'Reason',
            typeOperation: 'Operation Type',
            amount: 'amount',
            resetPassword: 'Reset password',
            calendar: 'calendar',
            related: 'related',
            last: 'last',
            inventory: 'inventory',
            validate: 'validate',
            missing: 'missing',
            edit: 'edit',
            show: 'show',
            manager: 'Manager',
            neighborhood: 'Neighborhood',
            houseNumber: 'House number',
            activityType: "Type of activity",
            currency: 'Currency',
            localisation: 'Location ',
            level: "Access level",
            image: 'Image',
            security: 'Security',
            gender: 'Gender',
            birthday: 'Birthday',
            confirm: 'Confirm',
            userConfig: {
                print: 'Print complete information at the Point of Sale',
                reprint: 'Reprint Point of Sale receipt',
                stockMovement: 'Can make stock entry',
                accesPrice: 'Access to prices',
                language: 'Language'
            },
            catalog: 'Catalog',
            reportSalary:'Payment sheet',
            title: 'Title',
            trainer: 'Trainer',
            activity: 'Activities',
            salary: 'Salary',
            employeeObj:{
                times: 'Work Schedule',
                dialyExpen: 'Daily Expenses',
                user: 'Related User'
            },
            stockObj: {
                qtdRequested: 'Total quantity requested',
                qtdCanseled: 'Total quantity canceled',
                qtdConq: 'Total quantity purchased',
                prodMxProfit: 'Product with highest profit',
                prodMinProfit: 'Product with lowest profit',
                prodMxSale: 'Best-selling product',
                prodMinSale: 'Least-selling product',
                storeDestination: 'Destination Storage',
                dateTransfer: 'Transfer Date',
                itemsSold: 'Items Sold',
                qtdAvailable: 'Available Quantities',
                qtdSold: 'Quantity Sold',
                hasDiscount: 'This item is not discounted',
                stockUnevailable: 'Unavailable stock',
                stockAvailable: 'Available stock',
                itemDiscount: 'Discounted items'
            }
        },
        message: {
            notData: "No {colum} found with this {value} .",
            serverError: "Une erreur du serveur s'est produite",
            emptyInput: "The {name} field cannot be empty.",
            outStock: 'This product does not have enough stock',
            userWithOutAccess: 'User without access.'
        },
        permissions:{
            Edit: 'Edit',
            Create: 'Create',
            Archive: 'Archive',
            Delete: 'Delete',
            Active: 'Activate',
            Editcostprice: 'Edit purchase price',
            Stockentry: 'Make stock entry',
            Outofstock: 'Out of stock',
            Editcostsale: 'Edit sale price',
            Editsession: 'Edit cash sessions',
            Canmovemoney: 'Can move money',
            ReprintPointofSalereceipt: 'Can reprint invoice',
            Printcompleteinformationcompany: 'You can print the complete company information',
            Caneditpriceofproduct: 'You can edit price at POS',
            Closesession: 'Close sessions',
            Cancelinvoice: 'You can cancel invoice',
            Opensession: 'You can open session',
            Print: "Print",
            Cancel: "Cancel",
        },
        pdv: {
            openingDate: 'Opening date',
            closeDate: 'Closing date',
            openingBy: 'Opened by',
            formedMoney: 'formed cash'
        },
        phrases: {
            closeControl: "Close controle",
            typeProductName: 'Enter the name of the article',
            priceProduct: 'The price of the article',
            costPrice: 'Purchase price',
            salePrice: 'Sale price',
            totalCost: 'Total purchase',
            totalSale: 'Total sales',
            unitProfit: 'Unit profit',
            totalProfit: 'Total profit',
            openingDate: 'Opening date',
            closeDate: 'Closing date',
            openingBy: 'Opened by',
            feature: 'Feature',
            userExperience: 'User experience',
            securityUpdate: 'Security update',
            opSessionContr: 'Session control opening',
            amountOpining: 'Opening amount',
            companyName: "Company name",
            nifCompany: 'Identification Number',
            userInfo: 'User informations',
            confirmPassword: 'Confirm your password',
            orderDate: 'Order date',
            dueDate: 'Due date',
            restToPay: 'Rest to pay',
            visitSite: 'Visit our website',
            contactUs: 'Contact us'
        },
        login: {
            title: 'Identify Yourself',
            password: 'Password',
            info: [
                {
                    title: 'Boost Your Sales with Our Advanced POS Management Software.',
                    description: 'Increase the efficiency of your sales with the latest generation POS Module, integrated with our complete management software.'
                },
                {
                    title: 'Financial Control Simplified with Our Invoice Management Software.',
                    description: 'Simplify invoicing and keep your finances in order with the Invoicing Module of our comprehensive management software.'
                },
                {
                    title: 'Optimize Your Purchasing with Our Purchasing Process Management Software.',
                    description: 'Improve your purchasing process efficiently using the Purchasing Module of our management software, integrating all steps in a simplified way.'
                },
                {
                    title: 'Explore New Possibilities with the Latest Feature of Our Management Software.',
                    description: 'Maximize user experience with our latest functionality, integrated with our advanced management software, delivering innovation and efficiency.'
                },
                {
                    title: 'Optimize Total Control of Your Inventory with Our Integrated Management Module.',
                    description: "Keep your products under control and reduce the risk of shortages or excess with the Stock and Inventory Management Module of our advanced management software. Get complete visibility, real-time updates and intuitive tools to improve your company's operational efficiency."
                }
            ]
        }
    }
};
const locale = localStorage.getItem('locale');
const i18n = createI18n({
    legacy: false,
    locale: locale || 'en',
    messages,
});

export default i18n;
