# Dictionnaire de données

## User

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|Username|VARCHAR(60)|PRIMARY KEY, NOT NULL|L'identifiant du Joueur|
|Password|VARCHAR(180)|NOT NULL|mot de passe du joueur crypté|
|Email|STRING(180)|NOT NULL|EMAIL|
|ROLE|LONGTEXT|NULL|Role de l'utilisateur (DMFC ou player)|
|Title|VARCHAR(60)| NULL|titre accordé par le maitre de jeux|
|Score|SMALLINT|NOT NULL (default:0)|Score du joueur|
|Old_Position|SMALLINT|NULL| Ancienne position du joueur dans le classement|
|Position|SMALLINT|NULL|Position du joueur dans le classement|
|Season_played|SMALLINT|NOT NULL (default:0)|Nombre de saison jouée|
|Team|ENTITY|NULL|L'équipe favorite du joueur (trigramme)|
|League|ENTITY|NOT NULL|Ligue du joueur (League_name)|
|Created_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La date de création de L'utilisateur|
|Updated_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La dernière date de modification de l'utilisateur|

## League

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|League_name|VARCHAR(180)|PRIMARY KEY, NOT NULL |nom de la ligue|
|League_description|VARCHAR(180)|NOT NULL |Description de la ligue|
|Created_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La date de création de La ligue|
|Updated_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|la dernière date de modification de la ligue|

## Season

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|int|NOT NULL (AUTO INCREMENT)|identité unique|
|Year|VARCHAR(60)|NOT NULL|Année de la saison|
|Created_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La date de création de La saison|
|Updated_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La dernière date de modification de la saison|

## Round

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|Code_round|int|PRIMARY KEY, NOT NULL (AUTO INCREMENT)|identité unique|
|Name|VARCHAR(60)|NOT NULL|Nom du round|
|Category|VARCHAR(60)|NOT NULL|Choix de la catégorie du round (SR ou PO)|
|Season|ENTITY|NOT NULL|Saison dans laquelle se passe le round (Year )|
|League|ENTITY|NOT NULL|League dans laquelle se passe le round (League_name )|
|User|ENTITY|NOT NULL|Maitre de jeux qui crée un round (Username) |
|Created_at|TIMESTAMP|NOT NULL,DEFAULT CURRENT_TIMESTAMP|La date de création du round|
|Updated_at|TIMESTAMP|NOT NULL,DEFAULT CURRENT_TIMESTAMP|La dernière date de modification du round|

## Match

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|Date_and_time_of_match|PRIMARY KEY, DATETIME|NOT NULL|Date et heure du match|
|Visitor_Score|SMALLINT|NULL|Score des visiteurs|
|Home_Score|SMALLINT|NULL|Score des receveurs|
|Winner|VARCHAR(60)|NULL|Vainqueur du match|
|Visitor_Odd|FLOAT| NULL|Cote de Las Vegas|
|Home_Odd|FLOAT| NULL|Cote de Las Vegas|
|Round|ENTITY| NOT NULL|Round du match (code_round)|
|Created_at|TIMESTAMP|NOT NULL,DEFAULT CURRENT_TIMESTAMP|La date de création du match|
|Updated_at|TIMESTAMP|NOT NULL,DEFAULT CURRENT_TIMESTAMP|La dernière date de modification du match|

## Team

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|Trigramme|VARCHAR(3)|PRIMARY KEY,NOT NULL |identité unique des équipes|
|Name|VARCHAR(60)|NOT NULL|Nom de l'équipe|
|Conference|VARCHAR(10)|NOT NULL|Nom de la conférence|
|Logo|VARCHAR(180)|NULL|Logo de l'équipe|
|NB_selected_Home|SMALLINT| NOT NULL (default:0)|Nombre de selection a domicile|
|NB_selected_Away|SMALLINT|NOT NULL (default:0)|Nombre de selection a l'extérieur|
|Classement|SMALLINT|NULL|Classement de la NBA|
|Match|ENTITY|NULL|Match joué par l'équipe (Date_and_time_of_match)|
|Created_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La date de création d'une équipe|
|Updated_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La dernière date de modification de l' équipe|

## SR Prediction

|Champ|Type|Spécificités|Description|
|-|-|-|-|
|id|int|NOT NULL (AUTO INCREMENT)|identité unique |
|Predicted_winnig_team|VARCHAR(180)|NOT NULL|Prédiction de l'équipe victorieuse|
|Predicted_point_difference|SMALLINT|NOT NULL|Prédiction de la différence de point|
|Validation_status_(saved, validated, published)|STRING|NOT NULL|Validation des prédictions|
|Point_scored|SMALLINT|NOT NULL (default:0)|Point de victoire|
|Bonus_points_erned |SMALLINT|NOT NULL(default:0)|Point bonus|
|Bonus_Bookie|SMALLINT|NOT NULL (default:0)|Point bonus cote Las Vegas|
|User|ENTITY|NOT NULL |Joueur qui pronostique (Username)|
|Match|ENTITY|NOT NULL |Match du pronostique (Date_and_time of_match)|
|Created_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La date de création d'une prédiction|
|Updated_at|TIMESTAMP|NOT NULL, DEFAULT CURRENT_TIMESTAMP|La dernière date de  modification d'une prédiction|