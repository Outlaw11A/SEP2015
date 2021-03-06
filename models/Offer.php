<?php

class Offer {

    /**
     * Gets the offer for a specific id for the specified user
     *
     * @param $id
     * @param $owner
     * @return null
     */
    static function getOffer($id, $owner) {

        $mysql = new MySQL();
        $results = $mysql->query('SELECT * FROM offer WHERE id = :id and owner = :owner', [':id' => $id, ':owner' => $owner]);

        if ($results['success'] == true && !empty($results['results']) && $results['results'] != null) {
            return $results['results'];
        }

        return null;

    }

    /**
     * Gets the offer for a specific id
     *
     * @param $id
     * @return null
     */
    static function getOfferSingle($id) {

        $mysql = new MySQL();
        $results = $mysql->query('SELECT * FROM offer WHERE id = :id', [':id' => $id]);

        if ($results['success'] == true && !empty($results['results']) && $results['results'] != null) {
            return $results['results'];
        }

        return null;

    }

    /**
     * Gets all offers for a specific owner
     *
     * @param $owner
     * @return null
     */
    static function getOffersForUser($owner) {

        $mysql = new MySQL();
        $results = $mysql->queryAll('SELECT * FROM offer WHERE owner = :owner', [':owner' => $owner]);

        if ($results['success'] == true && !empty($results['results']) && $results['results'] != null) {
            return $results['results'];
        }

        return null;

    }

    /**
     * Create a new offer, makes sure the user hasn't made an offer for this advertisement before.
     *
     * @param $owner
     * @param $advertisement
     * @param $description
     * @return bool
     */
    static function createOffer($owner, $advertisement, $description) {

        $offers = self::getOffersForUser($owner);

        foreach($offers as $offer) {
            if ($offer['advertisement'] == $advertisement) {
                return false;
            }
        }

        $mysql = new MySQL();
        $results = $mysql->query('INSERT INTO offer(id, owner, advertisement, description, status) VALUES (:id, :owner, :advertisement, :description, :status)', [
            ':id' => self::getNextId(),
            ':owner' => $owner,
            ':advertisement' => $advertisement,
            ':description' => $description,
            ':status' => 0
        ]);

        if ($results['success'] == true) {
            return true;
        }

        return false;

    }

    /**
     * Get the next offer id
     *
     * @return int|null
     */
    static function getNextId() {

        $mysql = new MySQL();
        $results = $mysql->query('SELECT id FROM offer ORDER BY id DESC', null);

        if ($results['success'] == true) {
            $id = (int) $results['results']['id'];
            $id++;
            return $id;
        }

        return null;

    }

    /**
     * Complete an offer if both ratings have been completed
     *
     * @param $id
     */
    static function completeOffer($id) {

        $offer = self::getOfferSingle($id);
        $advertisement = Advertisement::getAdvertisement($offer['advertisement']);
        if (Rating::getRating($id, $offer['owner']) != null && Rating::getRating($id, $advertisement['owner']) != null) {
            $mysql = new MySQL();
            $results = $mysql->query('UPDATE offer SET status = 3 WHERE id = :id', [
                ':id' => $id
            ]);
        }

    }

    static function getOffersForAdvertisement($id) {

        $mysql = new MySQL();
        $results = $mysql->queryAll('SELECT * FROM offer WHERE advertisement = :advertisement', [
            ':advertisement' => $id,
        ]);

        if ($results['success'] == true && !empty($results['results']) && $results['results'] != null) {
            return $results['results'];
        }

        return null;

    }

    /**
     * Accept an offer
     *
     * @param $id
     * @return mixed
     */
    static function acceptOffer($id) {

        $mysql = new MySQL();
        $results = $mysql->query('UPDATE offer SET status = 1 WHERE id = :id', [
            ':id' => $id
        ]);

        return $results['success'];

    }

    /**
     * Decline an offer
     *
     * @param $id
     * @return mixed
     */
    static function declineOffer($id) {

        $mysql = new MySQL();
        $results = $mysql->query('UPDATE offer SET status = 2 WHERE id = :id', [
            ':id' => $id
        ]);

        return $results['success'];

    }

}

?>