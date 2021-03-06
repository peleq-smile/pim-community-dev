<?php

namespace spec\Pim\Component\ReferenceData\Denormalizer\Standard\ProductValue;

use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Bundle\ReferenceDataBundle\Doctrine\ORM\Repository\ReferenceDataRepository;
use Pim\Component\ReferenceData\Model\ReferenceDataInterface;
use Pim\Component\ReferenceData\Repository\ReferenceDataRepositoryResolverInterface;

class ReferenceDataDenormalizerSpec extends ObjectBehavior
{
    function let(ReferenceDataRepositoryResolverInterface $resolver)
    {
        $this->beConstructedWith(['pim_reference_data_simpleselect'], $resolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Component\Catalog\Denormalizer\Standard\ProductValue\AbstractValueDenormalizer');
    }

    function it_is_a_denormalizer()
    {
        $this->shouldBeAnInstanceOf('Symfony\Component\Serializer\Normalizer\DenormalizerInterface');
    }

    function it_supports_denormalization_of_reference_data_values_from_standard()
    {
        $this->supportsDenormalization([], 'pim_reference_data_simpleselect', 'standard')->shouldReturn(true);
        $this->supportsDenormalization([], 'pim_catalog_text', 'standard')->shouldReturn(false);
        $this->supportsDenormalization([], 'pim_reference_data_simpleselect', 'csv')->shouldReturn(false);
    }

    function it_returns_null_if_data_is_empty()
    {
        $this->denormalize('', 'pim_reference_data_simpleselect', 'standard')->shouldReturn(null);
        $this->denormalize(null, 'pim_reference_data_simpleselect', 'standard')->shouldReturn(null);
        $this->denormalize([], 'pim_reference_data_simpleselect', 'standard')->shouldReturn(null);
    }

    function it_throws_an_exception_if_there_is_no_attribute_in_context()
    {
        $this->shouldThrow('\InvalidArgumentException')
            ->during(
                'denormalize',
                [
                    'battlecruiser',
                    'pim_reference_data_simpleselect',
                    'standard',
                    ['foo' => 'bar']
                ]
            );
    }

    function it_throws_an_exception_if_context_attribute_is_not_an_attribute()
    {
        $this->shouldThrow('\InvalidArgumentException')
            ->during(
                'denormalize',
                [
                    'battlecruiser',
                    'pim_reference_data_simpleselect',
                    'standard',
                    ['attribute' => 'bar']
                ]
            );
    }

    function it_denormalizes_data_into_reference_data(
        $resolver,
        AttributeInterface $attribute,
        ReferenceDataInterface $battlecruiser,
        ReferenceDataRepository $referenceDataRepo
    ) {
        $attribute->getReferenceDataName()->willReturn('starship');
        $resolver->resolve('starship')->willReturn($referenceDataRepo);
        $referenceDataRepo->findOneBy(['code' => 'battlecruiser'])->willReturn($battlecruiser);

        $this
            ->denormalize(
                'battlecruiser',
                'pim_reference_data_simpleselect',
                'standard',
                ['attribute' => $attribute]
            )
            ->shouldReturn($battlecruiser);
    }
}
