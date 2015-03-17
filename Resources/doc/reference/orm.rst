Translate Doctrine ORM models
=============================

Doctrine ORM models translations are handled by `Gedmo translatable extension <https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/translatable.md>`_ or `KnpLabs Doctrine2 Behaviors <https://github.com/KnpLabs/DoctrineBehaviors#translatable>`_.

Gedmo have two ways to handle translations.

Either everything is saved in a unique table, this is easier to set up but can lead to bad performance if your project
grows or it can have one translation table for every model table. This second way is called personal translation.

Doctrine Behaviours works with a translation table for every model. In your model you have the no translatable strings and in the translatable entity you have the translatable fields.

A. Using Gedmo translatable extension
-------------------------------------

1. Implement TranslatableInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

First step, your entities have to implement `TranslatableInterface <https://github.com/sonata-project/SonataTranslationBundle/blob/master/Model/Gedmo/TranslatableInterface.php>`_.

Todo do so SonataTranslationBundle brings some base classes you can extend.
Depends on how you want to save translations you can choose between :

* `Sonata\TranslationBundle\Model\Gedmo\AbstractTranslatable`
* `Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable`

**Here is an example of an entity using Personal Translation :**

.. code-block:: php

    namespace Presta\CMSFAQBundle\Entity;

    use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable;
    use Gedmo\Mapping\Annotation as Gedmo;
    use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;

    /**
     * @ORM\Table(name="presta_cms_faq_category")
     * @ORM\Entity(repositoryClass="Presta\CMSFAQBundle\Entity\FAQCategory\Repository")
     * @Gedmo\TranslationEntity(class="Presta\CMSFAQBundle\Entity\FAQCategory\Translation")
     */
    class FAQCategory extends AbstractPersonalTranslatable implements TranslatableInterface
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @var string $title
         *
         * @Gedmo\Translatable
         * @ORM\Column(name="title", type="string", length=255, nullable=true)
         */
        private $title;

        /**
         * @var boolean $enabled
         *
         * @ORM\Column(name="enabled", type="boolean", nullable=false)
         */
        private $enabled = false;

        /**
         * @var integer $position
         *
         * @ORM\Column(name="position", type="integer", length=2, nullable=true)
         */
        private $position;

        /**
         * @var ArrayCollection
         *
         * @ORM\OneToMany(
         *     targetEntity="Presta\CMSFAQBundle\Entity\FAQCategory\Translation",
         *     mappedBy="object",
         *     cascade={"persist", "remove"}
         * )
         */
        protected $translations;

        /* ... */
    }

**Note:** If your prefer to use `traits`, we provide :

* `Sonata\TranslationBundle\Traits\Translatable`
* `Sonata\TranslationBundle\Traits\PersonalTranslatable`


**Here is the same class using traits :**

.. code-block:: php

    namespace Presta\CMSFAQBundle\Entity;

    use Gedmo\Mapping\Annotation as Gedmo;
    use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;

    /**
     * @author Nicolas Bastien <nbastien@prestaconcept.net>
     *
     * @ORM\Table(name="presta_cms_faq_category")
     * @ORM\Entity(repositoryClass="Presta\CMSFAQBundle\Entity\FAQCategory\Repository")
     * @Gedmo\TranslationEntity(class="Presta\CMSFAQBundle\Entity\FAQCategory\Translation")
     */
    class FAQCategory  implements TranslatableInterface
    {
        use PersonalTranslatable;

        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /* ... */
    }


2. Define translated fields
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Please refer to `Gedmo translatable documentation <https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/translatable.md>`_.

3. Define your translation table
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Optinal, if you choose personal translation, you have to make a translation class to handle it.

**Here is the personal translation class for the example above :**

.. code-block:: php

    namespace Presta\CMSFAQBundle\Entity\FAQCategory;

    use Doctrine\ORM\Mapping as ORM;
    use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslation;

    /**
     * @ORM\Entity
     * @ORM\Table(name="presta_cms_faq_category_translation",
     *     uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_faq_category_translation_idx", columns={
     *         "locale", "object_id", "field"
     *     })}
     * )
     */
    class Translation extends AbstractPersonalTranslation
    {
        /**
         * @ORM\ManyToOne(targetEntity="Presta\CMSFAQBundle\Entity\FAQCategory", inversedBy="translations")
         * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
         */
        protected $object;
    }

B. Using KnpLabs Doctrine Behaviours
------------------------------------

1. Implement TranslatableInterface
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Like before, your entities have to implement `TranslatableInterface <https://github.com/sonata-project/SonataTranslationBundle/blob/master/Model/Gedmo/TranslatableInterface.php>`_.

And your entities have to implement get and set methods with this format because magic method of Doctrine Behaviour doesnt work (https://github.com/KnpLabs/DoctrineBehaviors#proxy-translations) due to one thing in the internals of Soanta. The explanation and solution for that, you can find it in this `post <http://thewebmason.com/tutorial-using-sonata-admin-with-magic-__call-method/>`_

.. code-block:: php

    namespace WAM\Bundle\DummyBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;
    use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;

    /**
     * @ORM\Table(name="wam_dummy_translatable_entity")
     * @ORM\Entity
     */
    class TranslatableEntity implements TranslatableInterface
    {
        use ORMBehaviors\Translatable\Translatable;

        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255)
         */
        private $noTranslatableString;

        /**
         * @return integer
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @return string
         */
        public function getNoTranslatableString()
        {
            return $this->noTranslatableString;
        }

        /**
         * @param string $noTranslatableString
         *
         * @return TranslatableEntity
         */
        public function setNoTranslatableString($noTranslatableString)
        {
            $this->noTranslatableString = $noTranslatableString;

            return $this;
        }

        /**
         * @return mixed
         */
        public function getName()
        {
            return $this->translate(null, false)->getName();
        }

        /**
         * @param string $name
         */
        public function setName($name)
        {
            $this->translate(null, false)->setName($name);

            return $this;
        }

        /**
         * @param string $locale
         */
        public function setLocale($locale)
        {
            $this->setCurrentLocale($locale);

            return $this;
        }

        /**
         * @return string
         */
        public function getLocale()
        {
            return $this->getCurrentLocale();
        }
    }


2. Define your translation table
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Please refer to `KnpLabs Doctrine2 Behaviors Documentation <https://github.com/KnpLabs/DoctrineBehaviors#translatable>`_.

Here is an example:

.. code-block:: php

    namespace WAM\Bundle\DummyBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Knp\DoctrineBehaviors\Model as ORMBehaviors;

    /**
     * @ORM\Table(name="wam_dummy_translatable_entity_translation")
     * @ORM\Entity
     */
    class TranslatableEntityTranslation
    {
        use ORMBehaviors\Translatable\Translation;

        /**
         * @var string
         *
         * @ORM\Column(type="string", length=255)
         */
        private $name;

        /**
         * @return integer
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param string $name
         *
         * @return TranslatableEntityTranslation
         */
        public function setName($name)
        {
            $this->name = $name;

            return $this;
        }
    }
