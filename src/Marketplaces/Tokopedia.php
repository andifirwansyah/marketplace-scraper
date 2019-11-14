<?php

namespace Marketplaces;

use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

class Tokopedia
{
    private $url;
    private $puppeteer;

    public function __construct($url)
    {
        $this->url = $url;
        $this->puppeteer = new Puppeteer();
    }

    public function getStoreInformation(){
        $browser = $this->puppeteer->launch(["headless" => false, "args" => ['--no-sandbox', '--disable-setuid-sandbox']]);
        $page = $browser->newPage();

        echo "Open page \n";

        $page->goto($this->url, array(
            "waitUntil" => "networkidle0",
            "timeout" => 0,
        ));

        echo "Open page finished \n";

        echo "Get store Information \n";
        
        // $page->waitForSelector(".css-z606dp-unf-btn", ['visible' => true]);

        $page->click(".css-z606dp-unf-btn");
        $page->waitForSelector(".css-149rvan-unf-modal",['visible' => true]);

        $store = $page->evaluate(JsFunction::createWithBody('
            return {
                "name": document.querySelector(".css-14uf4nq-unf-heading > span").innerText,
                "author": document.querySelector(".css-c04u4w-unf-heading").innerText,
                "information": document.querySelector(".css-1x8q13v-unf-heading > span").innerText,
                "location": document.querySelector(".css-1x8q13v-unf-heading > div").innerText,
                "store_image": document.querySelector(".css-1ew46s1-unf-img > img").getAttribute("src"),
                "join_date": document.querySelector(".css-jg7uhu-unf-heading").innerText,
                "store_url": window.location.href,
                "count_review": document.querySelector(".css-1oz5w6c-unf-heading").innerText
            }
        '));

        // var_dump($store);
        // die;

        // echo "Get Products link \n";

        // $links = [];

        // $page->waitForSelector(".c-pagination__btn .c-icon--arrow-forward", ['visible' => true]);


        // $productLinks = $page->evaluate(JsFunction::createWithBody('
        //     let links = [];
        //     document.querySelectorAll(".c-product-card__name.js-tracker-product-link").forEach(el => {
        //         links.push(el.getAttribute("href"));
        //     })

        //     return links;
        // '));

        // $links = array_merge($links, $productLinks);

        // $isDisabled = $page->evaluate(JsFunction::createWithBody('
        //     return !!document.querySelector(".c-pagination__btn .c-icon--arrow-forward").parentElement.getAttribute("disabled");
        // '));

        // while (!$isDisabled) {
        //     $page->waitForSelector(".c-pagination__btn .c-icon--arrow-forward", ['visible' => true]);
        //     $page->click(".c-pagination__btn .c-icon--arrow-forward");

        //     $page->waitForSelector(".c-product-card__name.js-tracker-product-link", ['visible' => true]);

        //     $products = $page->evaluate(JsFunction::createWithBody('
        //         let links = [];
        //         document.querySelectorAll(".c-product-card__name.js-tracker-product-link").forEach(el => {
        //             links.push(el.getAttribute("href"));
        //         })
        //         return links;
        //     '));

        //     $links = array_merge($links, $products);

        //     $isDisabled = $page->evaluate(JsFunction::createWithBody('
        //         return !!document.querySelector(".c-pagination__btn .c-icon--arrow-forward").parentElement.getAttribute("disabled");
        //     '));

        // }
       
        // $this->Productlinks = $links;
        
        $browser->close();

        return $this->formatOutput($store);
    }

    public function formatOutput($data){
        $result = array(
            "name" => $data['name'],
            "author" => $data['author'],
            "information" => $data['information'],
            "location" => $data['location'],
            "store_image" => $data['store_image'], 
            "join_date" => $data['join_date'], 
            "store_url" => $data['store_url'],
            "count_review" => intval(preg_replace("/[^A-Za-z0-9\  ]/", "", $data['count_review']))
        );

        return $result;
    }

}
