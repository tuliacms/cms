<?php

declare(strict_types=1);

namespace Tulia\Cms\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;
use Tulia\Cms\ContactForm\Domain\FieldsParser\FieldsParserInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\Model\Form;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Menu;
use Tulia\Cms\Node\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Model\Enum\NodePurposeEnum;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Rules\CanAddPurpose\CanImposePurposeInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeRepositoryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Service\SlugGeneratorStrategy\SlugGeneratorStrategyInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget;

/**
 * @author Adam Banaszkiewicz
 */
final class DefaultWebsiteDataFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly NodeRepositoryInterface $nodeRepository,
        private readonly SlugGeneratorStrategyInterface $slugGeneratorStrategy,
        private readonly CanImposePurposeInterface $canImposePurpose,
        private readonly FieldsParserInterface $fieldsParser,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $website = $this->getWebsite();
        $user = $this->getUser();

        $contactForm = $this->getContactForm($user['email'], $website);
        $contactFormId = $contactForm->getId();
        $manager->persist($contactForm);

        $node = $this->getHomepageNode($user['id'], $website, $contactFormId);
        $homepageNodeId = $node->getId();
        $manager->persist($node);

        $node = $this->getAboutUsNode($user['id'], $website);
        $aboutUsNodeId = $node->getId();
        $manager->persist($node);

        $node = $this->getContactNode($user['id'], $website, $contactFormId);
        $contactNodeId = $node->getId();
        $manager->persist($node);

        $menu = $this->getMenu($website['code'], $aboutUsNodeId, $contactNodeId);
        $manager->persist($menu);
        $menuId = $menu->getId();

        $widget = $this->createWidget($website, $menuId);
        $manager->persist($widget);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['sample-website-data'];
    }

    private function getWebsite(): array
    {
        return $this->connection->fetchAssociative(
            'SELECT tl.*, tm.*, BIN_TO_UUID(tm.id) AS id
            FROM #__website AS tm
            INNER JOIN #__website_locale AS tl
            ON tl.website_id = tm.id AND tl.is_default = 1
            LIMIT 1'
        );
    }

    private function getUser(): array
    {
        return $this->connection->fetchAssociative('SELECT *, BIN_TO_UUID(id) AS id FROM #__user LIMIT 1');
    }

    private function getContactForm(string $email, array $website): Form
    {
        $contactForm = Form::create(
            (string) Uuid::v4(),
            'Contact form',
            'Contact form subject',
            $email,
            $website['name'],
            [$email],
            null,
            $this->fieldsParser,
            [
                ['name' => 'name', 'type' => 'text', 'options' => ['label' => 'Name', 'constraints' => 'required']],
                ['name' => 'message', 'type' => 'textarea', 'options' => ['label' => 'Message', 'constraints' => 'required']],
                ['name' => 'submit', 'type' => 'submit', 'options' => ['label' => 'Submit']],
            ],
            '<div class="mb-3">
    [name]
</div>
<div class="mb-3">
    [message]
</div>
[submit]',
            '{{ contact_form_fields() }}',
            $website['code'],
            $website['code'],
            [$website['code']]
        );
        return $contactForm;
    }

    private function getHomepageNode(
        $id,
        array $website,
        string $contactFormId
    ): Node {
        $node = $this->nodeRepository->create('page', $id, $website['id']);
        $node->persistPurposes($this->canImposePurpose, NodePurposeEnum::PAGE_HOMEPAGE);
        $node->publish(ImmutableDateTime::now());
        $trans = $node->translate($website['code']);
        $trans->rename($this->slugGeneratorStrategy, 'Homepage');
        $trans->persistAttributes(
            new Attribute(
                'content',
                'content',
                '<section class="tued-section" id="tued-section-3e8f3871-c8dc-4fb0-b67a-0a0cbf782d44"><div class="tued-container container-xxl"><div class="tued-row row" id="tued-row-d35cbc92-76ad-4cb3-998e-2b988e500044"><div class="tued-column col" id="tued-column-b6ff6780-1f58-4787-b524-75a139962bd3"><div class="tued-block" id="tued-block-19481744-9174-45d6-a0f2-0cce0c9c9bb0"><div class="block block-what-we-do"><div class="row"><div class="col-12 col-lg-6 order-lg-1"><div class="block-content"><p class="lead">What we do</p><h2>Mauris viverra ligula quis sollicitudin volutpat</h2><ul><li><strong>Phasellus tellus arcu</strong><br> Sed interdum augue sed laoreet malesuada. Phasellus tellus arcu, aliquam quis sollicitudin eu.</li><li><strong>Mauris tincidunt convallis</strong><br> Nunc ut dictum quam. Mauris tincidunt convallis lectus sed lacinia.</li></ul></div></div><div class="col-12 col-lg-6 order-lg-0 block-images"><div class="block-image block-image-main" id="tued-element-style-1"></div><div class="block-image block-image-sub" id="tued-element-style-2"></div></div></div></div></div></div></div></div></section><section class="tued-section" id="tued-section-cf2bdc07-a8ef-4b71-8abe-2d073d0edfc2"><div class="tued-container"><div class="tued-row row g-0" id="tued-row-226639ad-2705-4a9e-9733-c302d3726c43"><div class="tued-column col" id="tued-column-f6299382-a0f4-4aba-99dc-5ea4ce272383"><div class="tued-block" id="tued-block-5e35b9e4-12d2-4176-a4cf-f0beea13de46"><div class="block block-services block-bg-dark"><div class="container-xxl"><div class="row"><div class="col-12 col-lg-6"><p class="lead">Our Services</p><h2>Look What We Offer</h2></div><div class="col-12 col-lg-6"><p class="services-slogan">Ded nec finibus nulla. Fusce rhoncus dui eu nunc molestie, eget aliquet ligula mollis.</p></div></div><div class="services-collection"><div class="row"><div class="col-12 col-sm-12 col-md-6 col-lg-4"><div class="service-item service-item-hoverable"><div class="service-icon"><i class="far fa-money-bill-alt"></i></div><h3>Sed tempus libero id magna mattis</h3><p class="mb-3">Sed interdum augue sed laoreet malesuada. Phasellus tellus arcu, aliquam quis.</p></div></div><div class="col-12 col-sm-12 col-md-6 col-lg-4"><div class="service-item service-item-hoverable"><div class="service-icon"><i class="fas fa-shuttle-van"></i></div><h3>Proin ac dolor egestas</h3><p class="mb-3">Nunc ut quam. Mauris tincidunt convallis sed lacinia. Mauris viverra volutpat.</p></div></div><div class="col-12 col-sm-12 col-md-6 col-lg-4"><div class="service-item service-item-hoverable"><div class="service-icon"><i class="fas fa-fighter-jet"></i></div><h3>Mauris viverra ligula quis</h3><p class="mb-3">Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque iaculis.</p></div></div></div></div></div></div></div></div></div></div></section><section class="tued-section" id="tued-section-46e6ae17-9d43-4998-ba0a-a9b2ddee50b3"><div class="tued-container container-xxl"><div class="tued-row row" id="tued-row-75c3cb6d-9c09-45ac-a2cb-ed8907f8974b"><div class="tued-column col" id="tued-column-a91d1f91-f0da-4bb1-9509-60464bbfd45e"><div class="tued-block" id="tued-block-e8b3fb26-d24b-4c79-a23a-1fbdd5271098"><div class="block block-services-light"><div class="services-collection"><div class="row"><div class="col-12 col-lg-4 service-column"><div class="service-item"><div class="service-icon"><i class="far fa-money-bill-alt"></i></div><h3>Sed tempus libero</h3><p>Sed augue sed laoreet malesuada. Phasellus tellus arcu, aliquam interdum quis.</p></div></div><div class="col-12 col-lg-4 service-column"><div class="service-item"><div class="service-icon"><i class="fas fa-shuttle-van"></i></div><h3>Proin ac dolor egestas</h3><p>Nunc ut quam. Mauris tincidunt convallis sed lacinia. Mauris viverra volutpat.</p></div></div><div class="col-12 col-lg-4 service-column"><div class="service-item"><div class="service-icon"><i class="fas fa-fighter-jet"></i></div><h3>Mauris viverra ligula quis</h3><p>Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque iaculis.</p></div></div></div></div></div></div></div></div></div></section><section class="tued-section" id="tued-section-13a61cc5-edfd-42c9-b9a6-b6c42434a1a9"><div class="tued-container"><div class="tued-row row g-0" id="tued-row-0fa75a53-2281-4349-bff2-63076a191c0a"><div class="tued-column col" id="tued-column-d7a0af57-e172-44a9-bce0-4467eb67826c"><div class="tued-block" id="tued-block-92516719-8162-4951-b6f7-cd0912f65144"><div class="block block-bg-lightgray block-company-in-numbers"><div class="container-xxl"><div class="row"><div class="col"><p class="lead">Our history</p><h2>Our Company In Numbers</h2><div class="block-numbers row"><div class="block-number-item col-12 col-sm-6 col-xl-3"><div class="block-number-item-inner"><div class="block-number"><span class="block-number-counter">120</span><!--v-if--></div><div class="block-number-label">Realisations</div></div></div><div class="block-number-item col-12 col-sm-6 col-xl-3"><div class="block-number-item-inner"><div class="block-number"><span class="block-number-counter">50</span><!--v-if--></div><div class="block-number-label">Workers</div></div></div><div class="block-number-item col-12 col-sm-6 col-xl-3"><div class="block-number-item-inner"><div class="block-number"><span class="block-number-counter">5</span><!--v-if--></div><div class="block-number-label">Years experience</div></div></div><div class="block-number-item col-12 col-sm-6 col-xl-3"><div class="block-number-item-inner"><div class="block-number"><span class="block-number-counter">10</span><!--v-if--></div><div class="block-number-label">Countries</div></div></div></div></div></div></div></div></div></div></div></div></section><section class="tued-section" id="tued-section-e7e8396e-9961-4098-acda-4aad2651cb67"><div class="tued-container"><div class="tued-row row g-0" id="tued-row-08ee653d-ad99-4a89-bf75-cb813ebb9a78"><div class="tued-column col" id="tued-column-f831a6fb-ccf8-4da6-9fe3-f700a07b7468"><div class="tued-block" id="tued-block-e8ddf782-5f21-4e16-8236-dd3c7133e0a7"><div class="block block-bg-lightgray block-contact"><div class="container-xxl"><div class="row"><div class="col"><div class="tulia-container-max-width"><p class="lead">Any questions?</p><h2 class="">Contact us</h2><div></div><div class="tued-dynamic-block" data-tued-type="contact_form">[contact_form id="' . $contactFormId . '"]</div></div></div></div></div></div></div></div></div></div></section><style>#tued-block-19481744-9174-45d6-a0f2-0cce0c9c9bb0 #tued-element-style-1 {background-image:url(\'[image_url id="null" size="thumbnail"]\')}#tued-block-19481744-9174-45d6-a0f2-0cce0c9c9bb0 #tued-element-style-2 {background-image:url(\'[image_url id="null" size="thumbnail"]\')}</style>',
                '<section class="tued-section" id="tued-section-3e8f3871-c8dc-4fb0-b67a-0a0cbf782d44"><div class="tued-container container-xxl"><div class="tued-row row" id="tued-row-d35cbc92-76ad-4cb3-998e-2b988e500044"><div class="tued-column col" id="tued-column-b6ff6780-1f58-4787-b524-75a139962bd3"><div class="tued-block" id="tued-block-19481744-9174-45d6-a0f2-0cce0c9c9bb0"><div class="block block-what-we-do"><div class="row"><div class="col-12 col-lg-6 order-lg-1"><div class="block-content"><p class="lead">What we do</p><h2>Mauris viverra ligula quis sollicitudin volutpat</h2><ul><li><strong>Phasellus tellus arcu</strong><br> Sed interdum augue sed laoreet malesuada. Phasellus tellus arcu, aliquam quis sollicitudin eu.</li><li><strong>Mauris tincidunt convallis</strong><br> Nunc ut dictum quam. Mauris tincidunt convallis lectus sed lacinia.</li></ul></div></div><div class="col-12 col-lg-6 order-lg-0 block-images"><div class="block-image block-image-main" id="tued-element-style-1"></div><div class="block-image block-image-sub" id="tued-element-style-2"></div></div></div></div></div></div></div></div></section><section class="tued-section" id="tued-section-cf2bdc07-a8ef-4b71-8abe-2d073d0edfc2"><div class="tued-container"><div class="tued-row row g-0" id="tued-row-226639ad-2705-4a9e-9733-c302d3726c43"><div class="tued-column col" id="tued-column-f6299382-a0f4-4aba-99dc-5ea4ce272383"><div class="tued-block" id="tued-block-5e35b9e4-12d2-4176-a4cf-f0beea13de46"><div class="block block-services block-bg-dark"><div class="container-xxl"><div class="row"><div class="col-12 col-lg-6"><p class="lead">Our Services</p><h2>Look What We Offer</h2></div><div class="col-12 col-lg-6"><p class="services-slogan">Ded nec finibus nulla. Fusce rhoncus dui eu nunc molestie, eget aliquet ligula mollis.</p></div></div><div class="services-collection"><div class="row"><div class="col-12 col-sm-12 col-md-6 col-lg-4"><div class="service-item service-item-hoverable"><div class="service-icon"><i class="far fa-money-bill-alt"></i></div><h3>Sed tempus libero id magna mattis</h3><p class="mb-3">Sed interdum augue sed laoreet malesuada. Phasellus tellus arcu, aliquam quis.</p></div></div><div class="col-12 col-sm-12 col-md-6 col-lg-4"><div class="service-item service-item-hoverable"><div class="service-icon"><i class="fas fa-shuttle-van"></i></div><h3>Proin ac dolor egestas</h3><p class="mb-3">Nunc ut quam. Mauris tincidunt convallis sed lacinia. Mauris viverra volutpat.</p></div></div><div class="col-12 col-sm-12 col-md-6 col-lg-4"><div class="service-item service-item-hoverable"><div class="service-icon"><i class="fas fa-fighter-jet"></i></div><h3>Mauris viverra ligula quis</h3><p class="mb-3">Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque iaculis.</p></div></div></div></div></div></div></div></div></div></div></section><section class="tued-section" id="tued-section-46e6ae17-9d43-4998-ba0a-a9b2ddee50b3"><div class="tued-container container-xxl"><div class="tued-row row" id="tued-row-75c3cb6d-9c09-45ac-a2cb-ed8907f8974b"><div class="tued-column col" id="tued-column-a91d1f91-f0da-4bb1-9509-60464bbfd45e"><div class="tued-block" id="tued-block-e8b3fb26-d24b-4c79-a23a-1fbdd5271098"><div class="block block-services-light"><div class="services-collection"><div class="row"><div class="col-12 col-lg-4 service-column"><div class="service-item"><div class="service-icon"><i class="far fa-money-bill-alt"></i></div><h3>Sed tempus libero</h3><p>Sed augue sed laoreet malesuada. Phasellus tellus arcu, aliquam interdum quis.</p></div></div><div class="col-12 col-lg-4 service-column"><div class="service-item"><div class="service-icon"><i class="fas fa-shuttle-van"></i></div><h3>Proin ac dolor egestas</h3><p>Nunc ut quam. Mauris tincidunt convallis sed lacinia. Mauris viverra volutpat.</p></div></div><div class="col-12 col-lg-4 service-column"><div class="service-item"><div class="service-icon"><i class="fas fa-fighter-jet"></i></div><h3>Mauris viverra ligula quis</h3><p>Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque iaculis.</p></div></div></div></div></div></div></div></div></div></section><section class="tued-section" id="tued-section-13a61cc5-edfd-42c9-b9a6-b6c42434a1a9"><div class="tued-container"><div class="tued-row row g-0" id="tued-row-0fa75a53-2281-4349-bff2-63076a191c0a"><div class="tued-column col" id="tued-column-d7a0af57-e172-44a9-bce0-4467eb67826c"><div class="tued-block" id="tued-block-92516719-8162-4951-b6f7-cd0912f65144"><div class="block block-bg-lightgray block-company-in-numbers"><div class="container-xxl"><div class="row"><div class="col"><p class="lead">Our history</p><h2>Our Company In Numbers</h2><div class="block-numbers row"><div class="block-number-item col-12 col-sm-6 col-xl-3"><div class="block-number-item-inner"><div class="block-number"><span class="block-number-counter">120</span><!--v-if--></div><div class="block-number-label">Realisations</div></div></div><div class="block-number-item col-12 col-sm-6 col-xl-3"><div class="block-number-item-inner"><div class="block-number"><span class="block-number-counter">50</span><!--v-if--></div><div class="block-number-label">Workers</div></div></div><div class="block-number-item col-12 col-sm-6 col-xl-3"><div class="block-number-item-inner"><div class="block-number"><span class="block-number-counter">5</span><!--v-if--></div><div class="block-number-label">Years experience</div></div></div><div class="block-number-item col-12 col-sm-6 col-xl-3"><div class="block-number-item-inner"><div class="block-number"><span class="block-number-counter">10</span><!--v-if--></div><div class="block-number-label">Countries</div></div></div></div></div></div></div></div></div></div></div></div></section><section class="tued-section" id="tued-section-e7e8396e-9961-4098-acda-4aad2651cb67"><div class="tued-container"><div class="tued-row row g-0" id="tued-row-08ee653d-ad99-4a89-bf75-cb813ebb9a78"><div class="tued-column col" id="tued-column-f831a6fb-ccf8-4da6-9fe3-f700a07b7468"><div class="tued-block" id="tued-block-e8ddf782-5f21-4e16-8236-dd3c7133e0a7"><div class="block block-bg-lightgray block-contact"><div class="container-xxl"><div class="row"><div class="col"><div class="tulia-container-max-width"><p class="lead">Any questions?</p><h2 class="">Contact us</h2><div></div><div class="tued-dynamic-block" data-tued-type="contact_form">{{ contact_form(\'' . $contactFormId . '\') }}</div></div></div></div></div></div></div></div></div></div></section><style>#tued-block-19481744-9174-45d6-a0f2-0cce0c9c9bb0 #tued-element-style-1 {background-image:url(\'{{ image_url(\'null\', \'thumbnail\') }}\')}#tued-block-19481744-9174-45d6-a0f2-0cce0c9c9bb0 #tued-element-style-2 {background-image:url(\'{{ image_url(\'null\', \'thumbnail\') }}\')}</style>',
                ['sections'=>[0=>['rows'=>[0=>['columns'=>[0=>['id'=>'b6ff6780-1f58-4787-b524-75a139962bd3','type'=>'column','sizes'=>['xxl'=>['size'=>null],'xl'=>['size'=>null],'lg'=>['size'=>null],'md'=>['size'=>null],'sm'=>['size'=>null],'xs'=>['size'=>null]],'metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'row']],'style'=>[],'data'=>[],'blocks'=>[0=>['code'=>'tulia-lisa-whatwedoblock','data'=>['intro'=>'What we do','headline'=>'Mauris viverra ligula quis sollicitudin volutpat','image_under'=>['id'=>null,'filename'=>null],'image_above'=>['id'=>null,'filename'=>null],'content_list'=>[0=>['id'=>'1','lead'=>'Phasellus tellus arcu','paragraph'=>'Sed interdum augue sed laoreet malesuada. Phasellus tellus arcu, aliquam quis sollicitudin eu.'],1=>['id'=>'2','lead'=>'Mauris tincidunt convallis','paragraph'=>'Nunc ut dictum quam. Mauris tincidunt convallis lectus sed lacinia.']]],'id'=>'19481744-9174-45d6-a0f2-0cce0c9c9bb0','type'=>'block','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'column']],'style'=>['tued-element-style-1'=>[],'tued-element-style-2'=>[]]]]]],'id'=>'d35cbc92-76ad-4cb3-998e-2b988e500044','type'=>'row','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'section']],'style'=>[],'data'=>['gutters'=>'default']]],'id'=>'3e8f3871-c8dc-4fb0-b67a-0a0cbf782d44','type'=>'section','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>null,'id'=>null]],'data'=>['containerWidth'=>'default'],'style'=>[]],1=>['rows'=>[0=>['columns'=>[0=>['id'=>'f6299382-a0f4-4aba-99dc-5ea4ce272383','type'=>'column','sizes'=>['xxl'=>['size'=>null],'xl'=>['size'=>null],'lg'=>['size'=>null],'md'=>['size'=>null],'sm'=>['size'=>null],'xs'=>['size'=>null]],'metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'row']],'style'=>[],'data'=>[],'blocks'=>[0=>['code'=>'OurServices','data'=>['intro'=>'Our Services','headline'=>'Look What We Offer','short_text'=>'Ded nec finibus nulla. Fusce rhoncus dui eu nunc molestie, eget aliquet ligula mollis.','services'=>[0=>['id'=>'1','icon'=>'far fa-money-bill-alt','title'=>'Sed tempus libero id magna mattis','content'=>'Sed interdum augue sed laoreet malesuada. Phasellus tellus arcu, aliquam quis.','link'=>null],1=>['id'=>'2','icon'=>'fas fa-shuttle-van','title'=>'Proin ac dolor egestas','content'=>'Nunc ut quam. Mauris tincidunt convallis sed lacinia. Mauris viverra volutpat.','link'=>null],2=>['id'=>'3','icon'=>'fas fa-fighter-jet','title'=>'Mauris viverra ligula quis','content'=>'Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque iaculis.','link'=>null]]],'id'=>'5e35b9e4-12d2-4176-a4cf-f0beea13de46','type'=>'block','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'column']],'style'=>[]]]]],'id'=>'226639ad-2705-4a9e-9733-c302d3726c43','type'=>'row','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'section']],'style'=>[],'data'=>['gutters'=>'default']]],'id'=>'cf2bdc07-a8ef-4b71-8abe-2d073d0edfc2','type'=>'section','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>null,'id'=>null]],'data'=>['containerWidth'=>'full-width-no-padding'],'style'=>[]],2=>['rows'=>[0=>['columns'=>[0=>['id'=>'a91d1f91-f0da-4bb1-9509-60464bbfd45e','type'=>'column','sizes'=>['xxl'=>['size'=>null],'xl'=>['size'=>null],'lg'=>['size'=>null],'md'=>['size'=>null],'sm'=>['size'=>null],'xs'=>['size'=>null]],'metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'row']],'style'=>[],'data'=>[],'blocks'=>[0=>['code'=>'ServicesLight','data'=>['services'=>[0=>['id'=>'1','icon'=>'far fa-money-bill-alt','title'=>'Sed tempus libero','content'=>'Sed augue sed laoreet malesuada. Phasellus tellus arcu, aliquam interdum quis.'],1=>['id'=>'2','icon'=>'fas fa-shuttle-van','title'=>'Proin ac dolor egestas','content'=>'Nunc ut quam. Mauris tincidunt convallis sed lacinia. Mauris viverra volutpat.'],2=>['id'=>'3','icon'=>'fas fa-fighter-jet','title'=>'Mauris viverra ligula quis','content'=>'Interdum et malesuada fames ac ante ipsum primis in faucibus. Pellentesque iaculis.']]],'id'=>'e8b3fb26-d24b-4c79-a23a-1fbdd5271098','type'=>'block','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'column']],'style'=>[]]]]],'id'=>'75c3cb6d-9c09-45ac-a2cb-ed8907f8974b','type'=>'row','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'section']],'style'=>[],'data'=>['gutters'=>'default']]],'id'=>'46e6ae17-9d43-4998-ba0a-a9b2ddee50b3','type'=>'section','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>null,'id'=>null]],'data'=>['containerWidth'=>'default'],'style'=>[]],3=>['rows'=>[0=>['columns'=>[0=>['id'=>'d7a0af57-e172-44a9-bce0-4467eb67826c','type'=>'column','sizes'=>['xxl'=>['size'=>null],'xl'=>['size'=>null],'lg'=>['size'=>null],'md'=>['size'=>null],'sm'=>['size'=>null],'xs'=>['size'=>null]],'metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'row']],'style'=>[],'data'=>[],'blocks'=>[0=>['code'=>'CompanyInNumbers','data'=>['intro'=>'Our history','headline'=>'Our Company In Numbers','numbers'=>[0=>['id'=>'1','number'=>120,'label'=>'Realisations','suffix'=>null],1=>['id'=>'2','number'=>50,'label'=>'Workers','suffix'=>null],2=>['id'=>'3','number'=>5,'label'=>'Years experience','suffix'=>null],3=>['id'=>'4','number'=>10,'label'=>'Countries','suffix'=>null]]],'id'=>'92516719-8162-4951-b6f7-cd0912f65144','type'=>'block','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'column']],'style'=>[]]]]],'id'=>'0fa75a53-2281-4349-bff2-63076a191c0a','type'=>'row','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'section']],'style'=>[],'data'=>['gutters'=>'default']]],'id'=>'13a61cc5-edfd-42c9-b9a6-b6c42434a1a9','type'=>'section','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>null,'id'=>null]],'data'=>['containerWidth'=>'full-width-no-padding'],'style'=>[]],4=>['rows'=>[0=>['columns'=>[0=>['id'=>'f831a6fb-ccf8-4da6-9fe3-f700a07b7468','type'=>'column','sizes'=>['xxl'=>['size'=>null],'xl'=>['size'=>null],'lg'=>['size'=>null],'md'=>['size'=>null],'sm'=>['size'=>null],'xs'=>['size'=>null]],'metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'row']],'style'=>[],'data'=>[],'blocks'=>[0=>['code'=>'ContactForm','data'=>['intro'=>'Any questions?','headline'=>'Contact us','headline_justify'=>'left','content'=>null,'form_id'=>$contactFormId],'id'=>'e8ddf782-5f21-4e16-8236-dd3c7133e0a7','type'=>'block','metadata'=>['hovered'=>false,'selected'=>true,'parent'=>['type'=>'column']],'style'=>[]]]]],'id'=>'08ee653d-ad99-4a89-bf75-cb813ebb9a78','type'=>'row','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'section']],'style'=>[],'data'=>['gutters'=>'default']]],'id'=>'e7e8396e-9961-4098-acda-4aad2651cb67','type'=>'section','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>null,'id'=>null]],'data'=>['containerWidth'=>'full-width-no-padding'],'style'=>[]]]],
                [0=>'compilable',1=>'renderable']
            )
        );

        return $node;
    }

    private function getAboutUsNode(
        $id,
        array $website
    ): Node {
        $node = $this->nodeRepository->create('page', $id, $website['id']);
        $node->publish(ImmutableDateTime::now());
        $trans = $node->translate($website['code']);
        $trans->rename($this->slugGeneratorStrategy, 'About us');
        $trans->persistAttributes(
            new Attribute(
                'content',
                'content',
                '<section class="tued-section" id="tued-section-79ce0731-9de2-4f09-9682-1de14189d0e0"><div class="tued-container container-xxl"><div class="tued-row row" id="tued-row-2541e07e-d0ad-45b5-b535-0d8cc35c7e47"><div class="tued-column col" id="tued-column-c4078fc6-53a7-4c2e-8511-d74e6eeffaef"><div class="tued-block" id="tued-block-32bc599e-ee23-41cf-9d7f-3274ae962106"><div class="block block-text"><div class="container-xxl"><div class="row"><div class="col"><p class="lead">What we do?</p><h2 class="">About us</h2><div><h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h3><p>Nam feugiat convallis elit eget auctor. Praesent mi nisi, tempus at varius ac, fringilla eu neque. Maecenas vitae massa dapibus, mollis elit eu, condimentum magna. Cras non justo et dui sagittis viverra non non est. Donec scelerisque interdum neque, ac semper mi fermentum et. Quisque porta dolor quis efficitur venenatis. Morbi mattis mattis magna, vel euismod sapien blandit non. Integer eu eleifend diam. Nam ullamcorper sem sed enim iaculis posuere.</p></div></div></div></div></div></div></div></div></div></section><style></style>',
                '<section class="tued-section" id="tued-section-79ce0731-9de2-4f09-9682-1de14189d0e0"><div class="tued-container container-xxl"><div class="tued-row row" id="tued-row-2541e07e-d0ad-45b5-b535-0d8cc35c7e47"><div class="tued-column col" id="tued-column-c4078fc6-53a7-4c2e-8511-d74e6eeffaef"><div class="tued-block" id="tued-block-32bc599e-ee23-41cf-9d7f-3274ae962106"><div class="block block-text"><div class="container-xxl"><div class="row"><div class="col"><p class="lead">What we do?</p><h2 class="">About us</h2><div><h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h3><p>Nam feugiat convallis elit eget auctor. Praesent mi nisi, tempus at varius ac, fringilla eu neque. Maecenas vitae massa dapibus, mollis elit eu, condimentum magna. Cras non justo et dui sagittis viverra non non est. Donec scelerisque interdum neque, ac semper mi fermentum et. Quisque porta dolor quis efficitur venenatis. Morbi mattis mattis magna, vel euismod sapien blandit non. Integer eu eleifend diam. Nam ullamcorper sem sed enim iaculis posuere.</p></div></div></div></div></div></div></div></div></div></section><style></style>',
                ['sections'=>[0=>['rows'=>[0=>['columns'=>[0=>['id'=>'c4078fc6-53a7-4c2e-8511-d74e6eeffaef','type'=>'column','sizes'=>['xxl'=>['size'=>NULL],'xl'=>['size'=>NULL],'lg'=>['size'=>NULL],'md'=>['size'=>NULL],'sm'=>['size'=>NULL],'xs'=>['size'=>NULL]],'metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'row']],'style'=>[],'data'=>[],'blocks'=>[0=>['code'=>'Text','data'=>['intro'=>'What we do?','headline'=>'About us','headline_justify'=>'left','content'=>'<h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h3><p>Nam feugiat convallis elit eget auctor. Praesent mi nisi, tempus at varius ac, fringilla eu neque. Maecenas vitae massa dapibus, mollis elit eu, condimentum magna. Cras non justo et dui sagittis viverra non non est. Donec scelerisque interdum neque, ac semper mi fermentum et. Quisque porta dolor quis efficitur venenatis. Morbi mattis mattis magna, vel euismod sapien blandit non. Integer eu eleifend diam. Nam ullamcorper sem sed enim iaculis posuere.</p>'],'id'=>'32bc599e-ee23-41cf-9d7f-3274ae962106','type'=>'block','metadata'=>['hovered'=>false,'selected'=>true,'parent'=>['type'=>'column']],'style'=>[]]]]],'id'=>'2541e07e-d0ad-45b5-b535-0d8cc35c7e47','type'=>'row','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'section']],'style'=>[],'data'=>['gutters'=>'default']]],'id'=>'79ce0731-9de2-4f09-9682-1de14189d0e0','type'=>'section','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>NULL,'id'=>NULL]],'data'=>['containerWidth'=>'default'],'style'=>[]]]],
                [0=>'compilable',1=>'renderable']
            )
        );

        return $node;
    }

    private function getContactNode(
        $id,
        array $website,
        string $contactFormId
    ): Node {
        $node = $this->nodeRepository->create('page', $id, $website['id']);
        $node->publish(ImmutableDateTime::now());
        $trans = $node->translate($website['code']);
        $trans->rename($this->slugGeneratorStrategy, 'Contact');
        $trans->persistAttributes(
            new Attribute(
                'content',
                'content',
                '<section class="tued-section" id="tued-section-615e2551-8bd5-4cf4-9623-057048b4b727"><div class="tued-container"><div class="tued-row row g-0" id="tued-row-4eca4ffb-f023-4a5c-a32c-2bc89f1a36ec"><div class="tued-column col" id="tued-column-7c1f0df4-a3a3-466a-9feb-7584f9c14217"><div class="tued-block" id="tued-block-f8729375-d651-4bef-b265-a0eafb41aa2a"><div class="block block-bg-lightgray block-contact"><div class="container-xxl"><div class="row"><div class="col"><div class="tulia-container-max-width"><p class="lead">Any questions?</p><h2 class="">Contact us</h2><div><p>Proin lacus mi, fringilla ac maximus id, tincidunt vestibulum urna. Etiam et tellus libero. Etiam maximus ut tortor a sagittis. Sed semper, augue nec ultrices tincidunt, velit dui scelerisque lacus, id dignissim augue eros at nulla. Duis congue turpis a libero faucibus, nec semper ligula laoreet. Sed volutpat risus lorem, at viverra turpis pretium vitae. Ut vehicula, ex in tempor lacinia, nulla ante tempus purus, quis elementum ipsum leo varius eros. Mauris nec elementum lorem.</p></div><div class="tued-dynamic-block" data-tued-type="contact_form">[contact_form id="'.$contactFormId.'"]</div></div></div></div></div></div></div></div></div></div></section><style></style>',
                '<section class="tued-section" id="tued-section-615e2551-8bd5-4cf4-9623-057048b4b727"><div class="tued-container"><div class="tued-row row g-0" id="tued-row-4eca4ffb-f023-4a5c-a32c-2bc89f1a36ec"><div class="tued-column col" id="tued-column-7c1f0df4-a3a3-466a-9feb-7584f9c14217"><div class="tued-block" id="tued-block-f8729375-d651-4bef-b265-a0eafb41aa2a"><div class="block block-bg-lightgray block-contact"><div class="container-xxl"><div class="row"><div class="col"><div class="tulia-container-max-width"><p class="lead">Any questions?</p><h2 class="">Contact us</h2><div><p>Proin lacus mi, fringilla ac maximus id, tincidunt vestibulum urna. Etiam et tellus libero. Etiam maximus ut tortor a sagittis. Sed semper, augue nec ultrices tincidunt, velit dui scelerisque lacus, id dignissim augue eros at nulla. Duis congue turpis a libero faucibus, nec semper ligula laoreet. Sed volutpat risus lorem, at viverra turpis pretium vitae. Ut vehicula, ex in tempor lacinia, nulla ante tempus purus, quis elementum ipsum leo varius eros. Mauris nec elementum lorem.</p></div><div class="tued-dynamic-block" data-tued-type="contact_form">{{ contact_form(\''.$contactFormId.'\') }}</div></div></div></div></div></div></div></div></div></div></section><style></style>',
                ['sections'=>[0=>['rows'=>[0=>['columns'=>[0=>['id'=>'7c1f0df4-a3a3-466a-9feb-7584f9c14217','type'=>'column','sizes'=>['xxl'=>['size'=>NULL,],'xl'=>['size'=>NULL,],'lg'=>['size'=>NULL,],'md'=>['size'=>NULL,],'sm'=>['size'=>NULL,],'xs'=>['size'=>NULL,],],'metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'row',],],'style'=>[],'data'=>[],'blocks'=>[0=>['code'=>'ContactForm','data'=>['intro'=>'Any questions?','headline'=>'Contact us','headline_justify'=>'left','content'=>'<p>Proin lacus mi, fringilla ac maximus id, tincidunt vestibulum urna. Etiam et tellus libero. Etiam maximus ut tortor a sagittis. Sed semper, augue nec ultrices tincidunt, velit dui scelerisque lacus, id dignissim augue eros at nulla. Duis congue turpis a libero faucibus, nec semper ligula laoreet. Sed volutpat risus lorem, at viverra turpis pretium vitae. Ut vehicula, ex in tempor lacinia, nulla ante tempus purus, quis elementum ipsum leo varius eros. Mauris nec elementum lorem.</p>','form_id'=>'52157d68-a789-4a6c-844d-2e06047f4e21',],'id'=>'f8729375-d651-4bef-b265-a0eafb41aa2a','type'=>'block','metadata'=>['hovered'=>true,'selected'=>true,'parent'=>['type'=>'column',],],'style'=>[],],],],],'id'=>'4eca4ffb-f023-4a5c-a32c-2bc89f1a36ec','type'=>'row','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>'section',],],'style'=>[],'data'=>['gutters'=>'default',],],],'id'=>'615e2551-8bd5-4cf4-9623-057048b4b727','type'=>'section','metadata'=>['hovered'=>false,'selected'=>false,'parent'=>['type'=>NULL,'id'=>NULL,],],'data'=>['containerWidth'=>'full-width-no-padding',],'style'=>[],],],],
                [0=>'compilable',1=>'renderable']
            )
        );

        return $node;
    }

    private function getMenu(
        string $code,
        string $aboutUsNodeId,
        string $contactNodeId,
    ): Menu {
        $menu = Menu::create(
            (string)Uuid::v4(),
            'Main menu',
        );
        $item = $menu->createItem([$code], $code, 'Homepage');
        $item->linksTo('simple:homepage', '', '');
        $item->moveToPosition(1);
        $item = $menu->createItem([$code], $code, 'About us');
        $item->linksTo('node:page', $aboutUsNodeId, '');
        $item->moveToPosition(2);
        $item = $menu->createItem([$code], $code, 'Contact');
        $item->linksTo('node:page', $contactNodeId, '');
        $item->moveToPosition(2);
        return $menu;
    }

    private function createWidget(array $website, string $menuId): Widget
    {
        $widget = Widget::create(
            (string) Uuid::v4(),
            'internal.menu',
            'mainmenu',
            'Main menu',
            $website['code'],
            [$website['code']]
        );
        $widget->persistAttributes(
            $website['code'],
            $website['code'],
            [new Attribute('menu_id', 'menu_id', $menuId, null, [], [])]
        );

        return $widget;
    }
}
