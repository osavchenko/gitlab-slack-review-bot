<?php

declare(strict_types=1);

namespace App\ArgumentResolver;

use App\Constant\Event\ObjectKind;
use App\Entity\Author;
use App\Entity\Event\EventObject;
use App\Entity\Event\EventObjectAttributes;
use App\Entity\MergeRequest;
use App\Entity\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class MergeRequestArgumentValueResolver implements ValueResolverInterface
{
    private const CONTEXT = [UnwrappingDenormalizer::UNWRAP_PATH => '[object_attributes]'];
    private const PROJECT_CONTEXT = [UnwrappingDenormalizer::UNWRAP_PATH => '[project]'];

    private SerializerInterface $serializer;
    private PersistentEntityArgumentValueResolverHelper $entityArgumentValueResolverHelper;

    public function __construct(
        SerializerInterface $serializer,
        PersistentEntityArgumentValueResolverHelper $entityArgumentValueResolverHelper
    ) {
        $this->serializer = $serializer;
        $this->entityArgumentValueResolverHelper = $entityArgumentValueResolverHelper;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== MergeRequest::class) {
            return [];
        }

        /** @var EventObject $eventObject */
        $eventObject = $this->serializer->deserialize($request->getContent(), EventObject::class, 'json');

        if ($eventObject->getObjectKind() !== ObjectKind::MERGE_REQUEST) {
            return [];
        }

        $mergeRequest = $this->setMergeRequest($request);
        $this->setProject($request, $mergeRequest);
        $this->setAuthor($request, $mergeRequest);

        return [$mergeRequest];
    }

    protected function setMergeRequest(Request $request): MergeRequest
    {
        /**
         * @var MergeRequest $mergeRequest
         */
        $mergeRequest = $this->entityArgumentValueResolverHelper->deserializeAndPersist(
            $request->getContent(),
            MergeRequest::class,
            self::CONTEXT
        );

        return $mergeRequest;
    }

    protected function setProject(Request $request, MergeRequest $mergeRequest): void
    {
        /**
         * @var Project $project
         */
        $project = $this->entityArgumentValueResolverHelper->deserializeAndPersist(
            $request->getContent(),
            Project::class,
            self::PROJECT_CONTEXT
        );

        $mergeRequest->setProject($project);
    }
    protected function setAuthor(Request $request, MergeRequest $mergeRequest): void
    {
        /** @var EventObjectAttributes $eventObjectAttributes */
        $eventObjectAttributes = $this->serializer->deserialize(
            $request->getContent(),
            EventObjectAttributes::class,
            'json',
            self::CONTEXT
        );

        $author = new Author();
        $author->setId((int) $eventObjectAttributes->getAuthorId());

        /** @var Author $author */
        $author = $this->entityArgumentValueResolverHelper->persist(Author::class, $author);
        $mergeRequest->setAuthor($author);
    }
}
