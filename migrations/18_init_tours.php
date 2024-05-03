<?php

/**
 * Migration for importing premade tours into database
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
class InitTours extends Migration
{
    public function up()
    {
        $db = DBManager::get();
        // insert tours
        $query = 'INSERT INTO `help_tours` VALUES
        ("229e1046dd43e9d4b2e88ef9c1e532d3","861d6b27035ea0a766c2a60758fb6ad0","Marketplace - advanced search","test","tour","autor,tutor,dozent,admin,root",1,"en","5.4","demo-installation","root@localhost",1714318686,1714318986),
        ("82fb2ca48f408cd9666fe8fb611fd217","8762e459b64df52f48fcf9c61d552244","Marketplace - marketplaces","test","tour","autor,tutor,dozent,admin,root",1,"en","5.4","demo-installation","root@localhost",1714244246,1714260699),
        ("df23d8bdf718c8595fb4371df71ba432","9bbc509e19d7062d61b1e86361b1a2be","Marketplace - advanced search +","test","tour","autor,tutor,dozent,admin,root",1,"en","5.4","demo-installation","root@localhost",1714319057,1714319218),
        ("a9ef08ed6cda489c49af51d42fc0567b","9db8159b5fb602632c7a295f45812ba9","Marketplace - simple search","test","tour","autor,tutor,dozent,admin,root",1,"en","5.4","demo-installation","root@localhost",1714318368,1714318617),
        ("e3af82d522804d81d4d3184ee7936c8d","b18ce3ca031fcb72c7fe887ae10c3a2d","Marketplace - config -general","test","tour","admin,root",1,"en","5.4","demo-installation","root@localhost",1714262539,1714262937),
        ("10d5e0aa08685f2fc08694e56cd3d5f4","d67d514d2e5c45383fd82fca394a4310","Marketplace - Subscription settings","tewasd","tour","autor,tutor,dozent,admin,root",1,"en","5.4","demo-installation","root@localhost",1714262021,1714262378),
        ("171bfaf4d35ed29f542b7331c66f43f2","ea0225d372cd27dedd2310aff149675e","Marketplace - overview","asdsad","tour","autor,tutor,dozent,admin,root",1,"en","5.4","demo-installation","root@localhost",1714263059,1714263861)';


        $db->exec($query);

        // insert steps
        $db = DBManager::get();

        $query = 'INSERT INTO `help_tour_steps` VALUES ("861d6b27035ea0a766c2a60758fb6ad0",1,"Advanced search","In this search you can simply search for commodities by any attribute they have.","B",0,"","plugins.php/marketplace/advanced_search","","","root@localhost",1714318686,1714318790),("861d6b27035ea0a766c2a60758fb6ad0",2,"Text attribute","Text attribute has autocomple which suggest availible options. If you want to match partial word, you can add asterisk `*` to the part of the word where can be any number of any characters.","B",0,"#qs_567315371598ce08eb49ffdfebc96ff6_1","plugins.php/marketplace/advanced_search","","","root@localhost",1714318857,1714318986),("861d6b27035ea0a766c2a60758fb6ad0",3,"Categories","If category has any subcategory it will be shown next to category which you have selected.","B",0,"#search_category_select DIV:eq(0)  SELECT:eq(0)","plugins.php/marketplace/advanced_search","","","root@localhost",1714318950,1714318958),("8762e459b64df52f48fcf9c61d552244",1,"Catalogs","Here are displayed all of the different catalogs. In each catalog are commodities which are created by users.","B",0,"#tablesorterb20fb7fcbcc91caption","plugins.php/marketplace/marketplaces","#content TABLE:eq(0)  TBODY:eq(0)  TR:eq(0)  TD:eq(0)  A:eq(0)","#content","root@localhost",1714258888,1714259233),("8762e459b64df52f48fcf9c61d552244",2,"Bookmarks","Here are all of your saved commodities.","B",0,"#nav_marketplace_root_marketplace_my_bookmarks A:eq(0)","plugins.php/marketplace/marketplaces","","","root@localhost",1714260419,1714260452),("8762e459b64df52f48fcf9c61d552244",3,"Subscriptions","Here are commodities from all catalogs which are filtered by your subscribed tags and catagories.","B",0,"#nav_marketplace_root_my_subscriptions A:eq(0)  SPAN:eq(0)","plugins.php/marketplace/marketplaces","","","root@localhost",1714260500,1714260507),("8762e459b64df52f48fcf9c61d552244",4,"Subscription settings","Here you can set your subscriptions. If someone creates comodity that matches your settings you will get notification. Also all of the subscriptions can be viewed in \"My Subscriptions\" tab.","B",0,"#nav_marketplace_root_user_config A:eq(0)  SPAN:eq(0)","plugins.php/marketplace/marketplaces","","","root@localhost",1714260627,1714260634),("8762e459b64df52f48fcf9c61d552244",5,"Help","Most of the tabs have additional information that can help you use this plugin. It is accesible here.","B",0,"#helpbar_icon IMG:eq(0)","plugins.php/marketplace/marketplaces","","","root@localhost",1714260693,1714260699),("9bbc509e19d7062d61b1e86361b1a2be",1,"","In this search you can combine queries with logic operators (AND,OR,NOT). It has autocomplete which suggests availible properties, categories and tags.","B",0,"","plugins.php/marketplace/search","","","root@localhost",1714319057,1714319168),("9bbc509e19d7062d61b1e86361b1a2be",2,"","Information on how to use this search, can be seen here.","B",0,"#helpbar_icon IMG:eq(0)","plugins.php/marketplace/search","","","root@localhost",1714319213,1714319218),("9db8159b5fb602632c7a295f45812ba9",1,"Search engines","Here are all of the search engines in this plugin. You can search for commodities from this catalog.","B",0,"","plugins.php/marketplace/simple_search","","","root@localhost",1714318368,1714318452),("9db8159b5fb602632c7a295f45812ba9",2,"Search","This search simply searches in titles and descriptions of the commodities.","B",0,"#qs_dbc259d0fd18e91fa838bad615dee6d0_1","plugins.php/marketplace/simple_search","","","root@localhost",1714318510,1714318520),("9db8159b5fb602632c7a295f45812ba9",3,"","Here you can set how many results you want displayed.","B",0,"SELECT[name=limit]","plugins.php/marketplace/simple_search","","","root@localhost",1714318555,1714318561),("9db8159b5fb602632c7a295f45812ba9",4,"","Each search has additional information in here.","B",0,"#helpbar_icon IMG:eq(0)","plugins.php/marketplace/simple_search","","","root@localhost",1714318612,1714318617),("b18ce3ca031fcb72c7fe887ae10c3a2d",1,"Config","Here is global plugin configuration. \r\n\r\nWhen you are done, don\"t forget to save it on the bottom of the page.","B",0,"","plugins.php/marketplace/global_config","","","root@localhost",1714262539,1714262622),("b18ce3ca031fcb72c7fe887ae10c3a2d",2,"Catalogs","Here you can create and edit catalogs.","B",0,"#global_config H2:eq(0)","plugins.php/marketplace/global_config","","","root@localhost",1714262646,1714262655),("b18ce3ca031fcb72c7fe887ae10c3a2d",3,"Catalog configuration","Configuration of given catalog can be accesed here.","B",0,"#global_config UL:eq(0)  LI:eq(0)  SPAN:eq(0)  A:eq(0)","plugins.php/marketplace/global_config","","","root@localhost",1714262705,1714262710),("b18ce3ca031fcb72c7fe887ae10c3a2d",4,"Tags","Here you can manage tags from whole plugin.","B",0,"#global_config H2:eq(1)","plugins.php/marketplace/global_config","","","root@localhost",1714262767,1714262771),("b18ce3ca031fcb72c7fe887ae10c3a2d",5,"","Here you can delete unused tags. You can see how many times tag was used in `number of references` field.","B",0,"#global_config BUTTON:eq(7)","plugins.php/marketplace/global_config","","","root@localhost",1714262808,1714262875),("d67d514d2e5c45383fd82fca394a4310",1,"Subscription settings","Here you can set from which categories and tags, you will get notifications and what commodities will be displayed in \"My Subscriptions\" tab.","B",0,"","plugins.php/marketplace/user_config","","","root@localhost",1714262021,1714262126),("d67d514d2e5c45383fd82fca394a4310",2,"Tags","You can search all of the existing tags in this searchbar. When you click enter it will be added to your tags below. If you click on `x` on the tag you will remove it from your subscriptions.","B",0,"#search_input","plugins.php/marketplace/user_config","","","root@localhost",1714262193,1714262228),("d67d514d2e5c45383fd82fca394a4310",3,"","You can try it now","B",1,"","plugins.php/marketplace/user_config","","","root@localhost",1714262248,1714262248),("d67d514d2e5c45383fd82fca394a4310",4,"Categories","Here you can set to which categories you are subscribed.","B",0,"#categories_user_config H1:eq(0)","plugins.php/marketplace/user_config","","","root@localhost",1714262304,1714262330),("d67d514d2e5c45383fd82fca394a4310",5,"Saving","Dont forget to save your settings.","B",0,"BUTTON[name=save]","plugins.php/marketplace/user_config","","","root@localhost",1714262370,1714262378),("ea0225d372cd27dedd2310aff149675e",1,"Overview","Here are displayed all of the commodities in this catalog.","B",0,"#nav_marketplace_overview_all","plugins.php/marketplace/overview","","","root@localhost",1714263059,1714263420),("ea0225d372cd27dedd2310aff149675e",2,"","Here are your subscriptions from this catalog.","B",0,"#nav_marketplace_overview_my_subscriptions","plugins.php/marketplace/overview","","","root@localhost",1714263410,1714263424),("ea0225d372cd27dedd2310aff149675e",3,"","Here are commodities that you created in this catalog.","B",0,"#nav_marketplace_overview_my_demands","plugins.php/marketplace/overview","","","root@localhost",1714263468,1714263471),("ea0225d372cd27dedd2310aff149675e",4,"","Here are commodities from this catalog that you saved.","B",0,"#nav_marketplace_overview_my_bookmarks","plugins.php/marketplace/overview","","","root@localhost",1714263503,1714263510),("ea0225d372cd27dedd2310aff149675e",5,"","Here you can create new commodity.","B",0,"#link-3f072b2766c28da2f2ce54b1ffd55d27 A:eq(0)","plugins.php/marketplace/overview","","","root@localhost",1714263551,1714263556),("ea0225d372cd27dedd2310aff149675e",6,"","Here you can look into detail of commodity.","B",0,"#table_edit TABLE:eq(0)  THEAD:eq(0)  TR:eq(0)  TH:eq(0)","plugins.php/marketplace/overview","","","root@localhost",1714263581,1714263625),("ea0225d372cd27dedd2310aff149675e",7,"","Here you can save comodity into bookmarks.","B",0,"#table_edit TABLE:eq(0)  THEAD:eq(0)  TR:eq(0)  TH:eq(4)","plugins.php/marketplace/overview","","","root@localhost",1714263659,1714263670),("ea0225d372cd27dedd2310aff149675e",8,"","By this you can sort commodities.","B",0,"BUTTON[name=Sort]","plugins.php/marketplace/overview","","","root@localhost",1714263610,1714263722),("ea0225d372cd27dedd2310aff149675e",9,"","Here you can search for commodities.","B",0,"#nav_marketplace_3904a5f1be6b688e5aed9c5462e78cfc_user_config A:eq(0)","plugins.php/marketplace/overview","","","root@localhost",1714263804,1714263810),("ea0225d372cd27dedd2310aff149675e",10,"","If you want to return to list of catalogs, you can do it by this tab.","B",0,"#nav_marketplace_3904a5f1be6b688e5aed9c5462e78cfc_marketplaces A:eq(0)  SPAN:eq(0)","plugins.php/marketplace/overview","","","root@localhost",1714263854,1714263861);';


        $db->exec($query);

        //insert settings


        $query = 'INSERT INTO `help_tour_settings` VALUES ("861d6b27035ea0a766c2a60758fb6ad0",1,"autostart_once",1714318686,1714318695),("8762e459b64df52f48fcf9c61d552244",1,"autostart_once",1714244246,1714261322),("9bbc509e19d7062d61b1e86361b1a2be",1,"autostart_once",1714319057,1714319080),("9db8159b5fb602632c7a295f45812ba9",1,"autostart_once",1714318368,1714318378),("b18ce3ca031fcb72c7fe887ae10c3a2d",1,"autostart_once",1714262539,1714262565),("d67d514d2e5c45383fd82fca394a4310",1,"autostart_once",1714262021,1714262038),("ea0225d372cd27dedd2310aff149675e",1,"autostart_once",1714263059,1714263255);';
        $db->exec($query);
    }

    public function down()
    {
        // drop new tables
        DBManager::get()->exec('DELETE FROM help_tours WHERE tour_id in ("861d6b27035ea0a766c2a60758fb6ad0","8762e459b64df52f48fcf9c61d552244","9bbc509e19d7062d61b1e86361b1a2be","9db8159b5fb602632c7a295f45812ba9","b18ce3ca031fcb72c7fe887ae10c3a2d","d67d514d2e5c45383fd82fca394a4310","ea0225d372cd27dedd2310aff149675e")');
        DBManager::get()->exec('DELETE FROM help_tour_steps WHERE tour_id in ("861d6b27035ea0a766c2a60758fb6ad0","8762e459b64df52f48fcf9c61d552244","9bbc509e19d7062d61b1e86361b1a2be","9db8159b5fb602632c7a295f45812ba9","b18ce3ca031fcb72c7fe887ae10c3a2d","d67d514d2e5c45383fd82fca394a4310","ea0225d372cd27dedd2310aff149675e")');
        DBManager::get()->exec('DELETE FROM help_tour_settings WHERE tour_id in ("861d6b27035ea0a766c2a60758fb6ad0","8762e459b64df52f48fcf9c61d552244","9bbc509e19d7062d61b1e86361b1a2be","9db8159b5fb602632c7a295f45812ba9","b18ce3ca031fcb72c7fe887ae10c3a2d","d67d514d2e5c45383fd82fca394a4310","ea0225d372cd27dedd2310aff149675e")');
    }
}
